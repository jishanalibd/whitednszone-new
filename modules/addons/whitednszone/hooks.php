<?php
/**
 * WhiteDNSZone WHMCS Hooks
 *
 * Additional hooks for enhanced functionality
 */

use WHMCS\Database\Capsule;
use WHMCS\Application\Support\Facades\App;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Add custom sidebar to client area
 */
add_hook('ClientAreaSecondarySidebar', 1, function($secondarySidebar) {
    if (!is_null($secondarySidebar->getChild('Addon Module'))) {
        $panel = $secondarySidebar->getChild('Addon Module');
        
        if ($panel && strpos($_SERVER['REQUEST_URI'], 'whitednszone') !== false) {
            // Add quick actions panel
            $panel->addChild('whitednszone_quick', [
                'label' => 'Quick Actions',
                'order' => 1,
                'icon' => 'fa-bolt',
            ]);
        }
    }
});

/**
 * Add menu item to primary navigation
 */
add_hook('ClientAreaPrimaryNavbar', 1, function($primaryNavbar) {
    $navItem = $primaryNavbar->getChild('Support');
    if (is_null($navItem)) {
        $navItem = $primaryNavbar->addChild('dns', [
            'label' => 'DNS Management',
            'uri' => 'index.php?m=whitednszone',
            'order' => 50,
            'icon' => 'fa-globe',
        ]);
    } else {
        $navItem->addChild('dns_management', [
            'label' => 'DNS Zones',
            'uri' => 'index.php?m=whitednszone',
            'order' => 100,
            'icon' => 'fa-globe',
        ]);
    }
});

/**
 * Add admin menu item
 */
add_hook('AdminAreaClientSummaryPage', 1, function($vars) {
    $userId = $vars['userid'];
    
    // Count zones for this client
    $zoneCount = Capsule::table('mod_whitednszone_zones')
        ->where('userid', $userId)
        ->count();
    
    if ($zoneCount > 0) {
        return [
            'DNS Zones' => $zoneCount . ' zone(s) managed',
        ];
    }
    
    return [];
});

/**
 * Log DNS changes in activity log
 */
add_hook('DailyCronJob', 1, function() {
    // Clean up old audit logs (older than 1 year)
    $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));
    
    Capsule::table('mod_whitednszone_audit')
        ->where('created_at', '<', $oneYearAgo)
        ->delete();
    
    logActivity('WhiteDNSZone: Cleaned up old audit logs');
});

/**
 * Redirect WHMCS DNS Management to WhiteDNSZone
 *
 * When users click "DNS Management" in the client area,
 * they are redirected to the WhiteDNSZone module interface.
 * Automatically creates the zone if it doesn't exist.
 */
add_hook('ClientAreaPageDomainDNSManagement', 1, function($vars) {
    if ($_SESSION['uid'] && App::getCurrentFilename() == 'clientarea' && filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'domaindns') {

        $domain_id = filter_input(INPUT_GET, 'domainid', FILTER_VALIDATE_INT);

        if ($domain_id) {
            try {
                $userId = $_SESSION['uid'];
                
                // Get domain information
                $domainInfo = Capsule::table('tbldomains')
                    ->where('id', $domain_id)
                    ->where('userid', $userId)
                    ->first();
                
                if (!$domainInfo) {
                    // Domain not found or access denied
                    return;
                }
                
                $domain = $domainInfo->domain;
                
                // Check if zone already exists
                $existingZone = Capsule::table('mod_whitednszone_zones')
                    ->where('domain', $domain)
                    ->where('userid', $userId)
                    ->first();
                
                if (!$existingZone) {
                    // Get module configuration
                    $config = Capsule::table('tbladdonmodules')
                        ->where('module', 'whitednszone')
                        ->pluck('value', 'setting');
                    
                    // Check if auto-create is enabled
                    if (!empty($config['auto_create_zone']) && $config['auto_create_zone'] === 'on') {
                        // Load API client
                        require_once __DIR__ . '/lib/ApiClient.php';
                        
                        $apiClient = new \WhiteDNSZone\ApiClient(
                            $config['api_url'] ?? 'https://my.whitednszone.com/api',
                            $config['api_key'] ?? ''
                        );
                        
                        // Get default nameservers
                        $ns1 = $config['default_ns1'] ?? 'dns1.whitednszone.com';
                        $ns2 = $config['default_ns2'] ?? 'dns2.whitednszone.com';
                        
                        // Create zone via API
                        $result = $apiClient->createZone($domain, [$ns1, $ns2]);
                        
                        if ($result && isset($result['zone_id'])) {
                            // Save zone to database
                            $zoneId = Capsule::table('mod_whitednszone_zones')->insertGetId([
                                'userid' => $userId,
                                'domain' => $domain,
                                'zone_id' => $result['zone_id'],
                                'status' => 'active',
                                'nameservers' => json_encode([$ns1, $ns2]),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            
                            // Log the creation
                            Capsule::table('mod_whitednszone_audit')->insert([
                                'userid' => $userId,
                                'zone_id' => $zoneId,
                                'action' => 'zone_auto_created',
                                'details' => 'Zone automatically created on DNS management access',
                                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'system',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                            
                            logActivity('WhiteDNSZone: Auto-created zone for ' . $domain . ' on redirect (User ID: ' . $userId . ')');
                            
                            // Set existing zone for redirect
                            $existingZone = (object)['id' => $zoneId];
                        } else {
                            $error = $apiClient->getLastError();
                            logActivity('WhiteDNSZone: Failed to auto-create zone for ' . $domain . ' on redirect - ' . $error);
                        }
                    }
                }
                
                // Get system URL
                $system_url = Capsule::table('tblconfiguration')->select('value')->where('setting', 'SystemURL')->first()->value;

                // Redirect to WhiteDNSZone using standard WHMCS addon URL
                if ($existingZone) {
                    // Redirect directly to zone management
                    $url = rtrim($system_url, '/') . '/index.php?m=whitednszone&action=manage&zone_id=' . $existingZone->id;
                } else {
                    // Redirect to zones list
                    $url = rtrim($system_url, '/') . '/index.php?m=whitednszone';
                }

                header('Location: '.$url, true, '302');
                exit();
            } catch (\Exception $e) {
                logActivity('WhiteDNSZone: Error in ClientAreaPageDomainDNSManagement hook - ' . $e->getMessage());
                
                // Fallback redirect to zones list
                $system_url = Capsule::table('tblconfiguration')->select('value')->where('setting', 'SystemURL')->first()->value;
                $url = rtrim($system_url, '/') . '/index.php?m=whitednszone';
                header('Location: '.$url, true, '302');
                exit();
            }
        }
    }
});

/**
 * Auto-create DNS zone when domain is registered
 *
 * When a domain is registered through WHMCS, this hook automatically
 * creates a DNS zone in PowerDNS with default SOA and NS records.
 *
 * Only runs if "Auto Create DNS Zone" is enabled in module configuration.
 */
add_hook('AfterRegistrarRegistration', 1, function($vars) {
    try {
        // Get module configuration
        $config = Capsule::table('tbladdonmodules')
            ->where('module', 'whitednszone')
            ->pluck('value', 'setting');
        
        // Check if auto-create is enabled
        if (empty($config['auto_create_zone']) || $config['auto_create_zone'] !== 'on') {
            return;
        }
        
        // Get domain details
        $domain = $vars['params']['sld'] . '.' . $vars['params']['tld'];
        $userId = $vars['params']['userid'];
        
        // Check if zone already exists
        $existingZone = Capsule::table('mod_whitednszone_zones')
            ->where('domain', $domain)
            ->where('userid', $userId)
            ->first();
        
        if ($existingZone) {
            logActivity('WhiteDNSZone: Zone already exists for ' . $domain);
            return;
        }
        
        // Load API client
        require_once __DIR__ . '/lib/ApiClient.php';
        
        $apiClient = new \WhiteDNSZone\ApiClient(
            $config['api_url'] ?? 'https://my.whitednszone.com/api',
            $config['api_key'] ?? ''
        );
        
        // Get default nameservers
        $ns1 = $config['default_ns1'] ?? 'dns1.whitednszone.com';
        $ns2 = $config['default_ns2'] ?? 'dns2.whitednszone.com';
        
        // Create zone via API
        $result = $apiClient->createZone($domain, [$ns1, $ns2]);
        
        if ($result && isset($result['zone_id'])) {
            // Save zone to database
            $zoneId = Capsule::table('mod_whitednszone_zones')->insertGetId([
                'userid' => $userId,
                'domain' => $domain,
                'zone_id' => $result['zone_id'],
                'status' => 'active',
                'nameservers' => json_encode([$ns1, $ns2]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            // Log the creation
            Capsule::table('mod_whitednszone_audit')->insert([
                'userid' => $userId,
                'zone_id' => $zoneId,
                'action' => 'zone_auto_created',
                'details' => 'Zone automatically created after domain registration',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'system',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            logActivity('WhiteDNSZone: Auto-created zone for ' . $domain . ' (User ID: ' . $userId . ')');
        } else {
            $error = $apiClient->getLastError();
            logActivity('WhiteDNSZone: Failed to auto-create zone for ' . $domain . ' - ' . $error);
        }
    } catch (\Exception $e) {
        logActivity('WhiteDNSZone: Error in AfterRegistrarRegistration hook - ' . $e->getMessage());
    }
});
