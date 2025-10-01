<?php
/**
 * WHMCS WhiteDNSZone Addon Module
 *
 * @copyright Copyright (c) WhiteDNSZone
 * @license https://opensource.org/licenses/MIT MIT License
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Define addon module configuration parameters
 *
 * @return array
 */
function whitednszone_config()
{
    return [
        'name' => 'WhiteDNSZone Manager',
        'description' => 'Comprehensive DNS management addon for WhiteDNSZone API',
        'version' => '1.0.0',
        'author' => 'WhiteDNSZone',
        'language' => 'english',
        'fields' => [
            'api_url' => [
                'FriendlyName' => 'API URL',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'https://my.whitednszone.com/api',
                'Description' => 'WhiteDNSZone API endpoint URL',
            ],
            'api_key' => [
                'FriendlyName' => 'API Key',
                'Type' => 'password',
                'Size' => '50',
                'Description' => 'Your WhiteDNSZone API key',
            ],
            'default_ns1' => [
                'FriendlyName' => 'Default NS1',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'dns1.whitednszone.com',
                'Description' => 'Default primary nameserver',
            ],
            'default_ns2' => [
                'FriendlyName' => 'Default NS2',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'dns2.whitednszone.com',
                'Description' => 'Default secondary nameserver',
            ],
            'enable_audit_log' => [
                'FriendlyName' => 'Enable Audit Logging',
                'Type' => 'yesno',
                'Description' => 'Log all DNS changes for audit purposes',
            ],
            'enable_propagation_check' => [
                'FriendlyName' => 'Enable Propagation Check',
                'Type' => 'yesno',
                'Description' => 'Enable DNS propagation checking feature',
            ],
        ],
    ];
}

/**
 * Activate addon module
 *
 * @return array
 */
function whitednszone_activate()
{
    try {
        // Create zones table
        if (!Capsule::schema()->hasTable('mod_whitednszone_zones')) {
            Capsule::schema()->create('mod_whitednszone_zones', function ($table) {
                $table->increments('id');
                $table->integer('userid');
                $table->string('domain', 255);
                $table->string('zone_id', 100)->nullable();
                $table->string('status', 50)->default('active');
                $table->text('nameservers')->nullable();
                $table->timestamps();
                $table->index('userid');
                $table->index('domain');
            });
        }

        // Create records table
        if (!Capsule::schema()->hasTable('mod_whitednszone_records')) {
            Capsule::schema()->create('mod_whitednszone_records', function ($table) {
                $table->increments('id');
                $table->integer('zone_id');
                $table->string('record_id', 100)->nullable();
                $table->string('name', 255);
                $table->string('type', 10);
                $table->text('content');
                $table->integer('ttl')->default(3600);
                $table->integer('priority')->nullable();
                $table->timestamps();
                $table->index('zone_id');
                $table->index('type');
            });
        }

        // Create audit log table
        if (!Capsule::schema()->hasTable('mod_whitednszone_audit')) {
            Capsule::schema()->create('mod_whitednszone_audit', function ($table) {
                $table->increments('id');
                $table->integer('userid');
                $table->integer('zone_id')->nullable();
                $table->string('action', 100);
                $table->text('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index('userid');
                $table->index('zone_id');
                $table->index('created_at');
            });
        }

        // Create templates table
        if (!Capsule::schema()->hasTable('mod_whitednszone_templates')) {
            Capsule::schema()->create('mod_whitednszone_templates', function ($table) {
                $table->increments('id');
                $table->string('name', 100);
                $table->string('category', 50);
                $table->text('description')->nullable();
                $table->text('records');
                $table->boolean('is_preset')->default(false);
                $table->timestamps();
            });
        }

        // Insert preset templates
        $presets = [
            [
                'name' => 'Google Workspace',
                'category' => 'email',
                'description' => 'DNS records for Google Workspace email',
                'records' => json_encode([
                    ['type' => 'MX', 'name' => '@', 'content' => 'aspmx.l.google.com', 'priority' => 1, 'ttl' => 3600],
                    ['type' => 'MX', 'name' => '@', 'content' => 'alt1.aspmx.l.google.com', 'priority' => 5, 'ttl' => 3600],
                    ['type' => 'MX', 'name' => '@', 'content' => 'alt2.aspmx.l.google.com', 'priority' => 5, 'ttl' => 3600],
                    ['type' => 'MX', 'name' => '@', 'content' => 'alt3.aspmx.l.google.com', 'priority' => 10, 'ttl' => 3600],
                    ['type' => 'MX', 'name' => '@', 'content' => 'alt4.aspmx.l.google.com', 'priority' => 10, 'ttl' => 3600],
                ]),
                'is_preset' => true,
            ],
            [
                'name' => 'Office 365',
                'category' => 'email',
                'description' => 'DNS records for Microsoft Office 365',
                'records' => json_encode([
                    ['type' => 'MX', 'name' => '@', 'content' => '{domain}.mail.protection.outlook.com', 'priority' => 0, 'ttl' => 3600],
                    ['type' => 'TXT', 'name' => '@', 'content' => 'v=spf1 include:spf.protection.outlook.com -all', 'ttl' => 3600],
                    ['type' => 'CNAME', 'name' => 'autodiscover', 'content' => 'autodiscover.outlook.com', 'ttl' => 3600],
                ]),
                'is_preset' => true,
            ],
            [
                'name' => 'Mailgun',
                'category' => 'email',
                'description' => 'DNS records for Mailgun email service',
                'records' => json_encode([
                    ['type' => 'TXT', 'name' => '@', 'content' => 'v=spf1 include:mailgun.org ~all', 'ttl' => 3600],
                    ['type' => 'TXT', 'name' => 'mx._domainkey', 'content' => '{dkim_key}', 'ttl' => 3600],
                    ['type' => 'CNAME', 'name' => 'email', 'content' => 'mailgun.org', 'ttl' => 3600],
                ]),
                'is_preset' => true,
            ],
        ];

        foreach ($presets as $preset) {
            $existing = Capsule::table('mod_whitednszone_templates')
                ->where('name', $preset['name'])
                ->where('is_preset', true)
                ->first();
            
            if (!$existing) {
                Capsule::table('mod_whitednszone_templates')->insert($preset);
            }
        }

        return [
            'status' => 'success',
            'description' => 'WhiteDNSZone addon activated successfully. Database tables created.',
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Unable to activate: ' . $e->getMessage(),
        ];
    }
}

/**
 * Deactivate addon module
 *
 * @return array
 */
function whitednszone_deactivate()
{
    return [
        'status' => 'success',
        'description' => 'WhiteDNSZone addon deactivated. Database tables preserved.',
    ];
}

/**
 * Admin area output
 *
 * @param array $vars
 * @return array
 */
function whitednszone_output($vars)
{
    require_once __DIR__ . '/lib/Admin.php';
    $admin = new WhiteDNSZone\Admin($vars);
    return $admin->output();
}

/**
 * Client area output
 *
 * @param array $vars
 * @return array
 */
function whitednszone_clientarea($vars)
{
    require_once __DIR__ . '/lib/Client.php';
    
    $client = new WhiteDNSZone\Client($vars);
    return $client->output();
}

/**
 * Sidebar output
 *
 * @param array $vars
 * @return string
 */
function whitednszone_sidebar($vars)
{
    $sidebar = '<div class="panel panel-default">';
    $sidebar .= '<div class="panel-heading"><strong>WhiteDNSZone Quick Stats</strong></div>';
    $sidebar .= '<div class="panel-body">';
    
    try {
        $zoneCount = Capsule::table('mod_whitednszone_zones')
            ->where('userid', $_SESSION['uid'])
            ->count();
        
        $recordCount = Capsule::table('mod_whitednszone_records')
            ->join('mod_whitednszone_zones', 'mod_whitednszone_zones.id', '=', 'mod_whitednszone_records.zone_id')
            ->where('mod_whitednszone_zones.userid', $_SESSION['uid'])
            ->count();
        
        $sidebar .= '<p><strong>Total Zones:</strong> ' . $zoneCount . '</p>';
        $sidebar .= '<p><strong>Total Records:</strong> ' . $recordCount . '</p>';
    } catch (\Exception $e) {
        $sidebar .= '<p class="text-danger">Unable to load statistics</p>';
    }
    
    $sidebar .= '</div></div>';
    
    return $sidebar;
}
