<?php
/**
 * WhiteDNSZone WHMCS Hooks
 *
 * Additional hooks for enhanced functionality
 */

use WHMCS\Database\Capsule;

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
