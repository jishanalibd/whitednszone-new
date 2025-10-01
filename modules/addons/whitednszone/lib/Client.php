<?php

namespace WhiteDNSZone;

use WHMCS\Database\Capsule;

/**
 * Client Area Handler
 */
class Client
{
    private $vars;
    private $apiClient;
    private $userId;

    public function __construct($vars)
    {
        $this->vars = $vars;
        $this->userId = $_SESSION['uid'] ?? 0;
        
        require_once __DIR__ . '/ApiClient.php';
        $this->apiClient = new ApiClient(
            $vars['api_url'] ?? 'https://my.whitednszone.com/api',
            $vars['api_key'] ?? ''
        );
    }

    /**
     * Main output handler
     *
     * @return array
     */
    public function output()
    {
        $action = $_REQUEST['action'] ?? 'zones';
        
        // Handle AJAX requests
        if (isset($_REQUEST['ajax'])) {
            return $this->handleAjax();
        }
        
        switch ($action) {
            case 'zones':
                return $this->listZones();
            case 'manage':
                return $this->manageZone();
            default:
                return $this->listZones();
        }
    }

    /**
     * Handle AJAX requests
     */
    private function handleAjax()
    {
        header('Content-Type: application/json');
        
        $action = $_REQUEST['ajax_action'] ?? '';
        $result = ['success' => false, 'message' => 'Invalid action'];
        
        try {
            switch ($action) {
                case 'get_records':
                    $result = $this->ajaxGetRecords();
                    break;
                case 'add_record':
                    $result = $this->ajaxAddRecord();
                    break;
                case 'update_record':
                    $result = $this->ajaxUpdateRecord();
                    break;
                case 'delete_record':
                    $result = $this->ajaxDeleteRecord();
                    break;
                case 'bulk_delete':
                    $result = $this->ajaxBulkDelete();
                    break;
                case 'apply_template':
                    $result = $this->ajaxApplyTemplate();
                    break;
                case 'check_propagation':
                    $result = $this->ajaxCheckPropagation();
                    break;
                case 'get_dnssec':
                    $result = $this->ajaxGetDNSSEC();
                    break;
                case 'toggle_dnssec':
                    $result = $this->ajaxToggleDNSSEC();
                    break;
                case 'get_audit_log':
                    $result = $this->ajaxGetAuditLog();
                    break;
                case 'validate_record':
                    $result = $this->ajaxValidateRecord();
                    break;
            }
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }
        
        echo json_encode($result);
        exit;
    }

    /**
     * List zones
     */
    private function listZones()
    {
        $zones = Capsule::table('mod_whitednszone_zones')
            ->where('userid', $this->userId)
            ->orderBy('domain')
            ->get();
        
        return [
            'pagetitle' => 'DNS Zone Management',
            'breadcrumb' => ['index.php?m=whitednszone' => 'DNS Zones'],
            'templatefile' => 'templates/zones',
            'requirelogin' => true,
            'vars' => [
                'zones' => $zones,
                'modulelink' => $this->vars['modulelink'],
            ],
        ];
    }

    /**
     * Manage individual zone
     */
    private function manageZone()
    {
        $zoneId = (int)($_REQUEST['zone_id'] ?? 0);
        
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return [
                'pagetitle' => 'Zone Not Found',
                'templatefile' => 'templates/error',
                'requirelogin' => true,
                'vars' => [
                    'error' => 'Zone not found or access denied',
                ],
            ];
        }
        
        // Get templates
        $templates = Capsule::table('mod_whitednszone_templates')
            ->where('is_preset', true)
            ->get();
        
        return [
            'pagetitle' => 'Manage DNS - ' . $zone->domain,
            'breadcrumb' => [
                'index.php?m=whitednszone' => 'DNS Zones',
                'index.php?m=whitednszone&action=manage&zone_id=' . $zoneId => $zone->domain,
            ],
            'templatefile' => 'templates/manage',
            'requirelogin' => true,
            'vars' => [
                'zone' => $zone,
                'zone_id' => $zoneId,
                'templates' => $templates,
                'modulelink' => $this->vars['modulelink'],
                'default_ns1' => $this->vars['default_ns1'] ?? 'dns1.whitednszone.com',
                'default_ns2' => $this->vars['default_ns2'] ?? 'dns2.whitednszone.com',
                'enable_propagation_check' => $this->vars['enable_propagation_check'] ?? false,
            ],
        ];
    }

    /**
     * AJAX: Get records
     */
    private function ajaxGetRecords()
    {
        $zoneId = (int)($_REQUEST['zone_id'] ?? 0);
        
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return ['success' => false, 'message' => 'Zone not found'];
        }
        
        $records = Capsule::table('mod_whitednszone_records')
            ->where('zone_id', $zoneId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        
        return ['success' => true, 'records' => $records];
    }

    /**
     * AJAX: Add record
     */
    private function ajaxAddRecord()
    {
        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $content = $_POST['content'] ?? '';
        $ttl = (int)($_POST['ttl'] ?? 3600);
        $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : null;
        
        // Validate zone access
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return ['success' => false, 'message' => 'Zone not found'];
        }
        
        // Validate record
        $validation = $this->validateRecord($type, $name, $content, $priority);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Create record via API
        $recordData = [
            'name' => $name,
            'type' => $type,
            'content' => $content,
            'ttl' => $ttl,
        ];
        
        if ($priority !== null && in_array($type, ['MX', 'SRV'])) {
            $recordData['priority'] = $priority;
        }
        
        $apiResult = $this->apiClient->createRecord($zone->zone_id, $recordData);
        
        if ($apiResult === false) {
            return ['success' => false, 'message' => $this->apiClient->getLastError()];
        }
        
        // Store in local database
        $recordId = Capsule::table('mod_whitednszone_records')->insertGetId([
            'zone_id' => $zoneId,
            'record_id' => $apiResult['id'] ?? null,
            'name' => $name,
            'type' => $type,
            'content' => $content,
            'ttl' => $ttl,
            'priority' => $priority,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Log action
        $this->logAudit($zoneId, 'record_create', "Created $type record: $name");
        
        return ['success' => true, 'message' => 'Record created successfully', 'record_id' => $recordId];
    }

    /**
     * AJAX: Update record
     */
    private function ajaxUpdateRecord()
    {
        $recordId = (int)($_POST['record_id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $content = $_POST['content'] ?? '';
        $ttl = (int)($_POST['ttl'] ?? 3600);
        $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : null;
        
        // Get record and validate access
        $record = Capsule::table('mod_whitednszone_records')
            ->join('mod_whitednszone_zones', 'mod_whitednszone_zones.id', '=', 'mod_whitednszone_records.zone_id')
            ->where('mod_whitednszone_records.id', $recordId)
            ->where('mod_whitednszone_zones.userid', $this->userId)
            ->select('mod_whitednszone_records.*', 'mod_whitednszone_zones.zone_id as api_zone_id')
            ->first();
        
        if (!$record) {
            return ['success' => false, 'message' => 'Record not found'];
        }
        
        // Validate record
        $validation = $this->validateRecord($type, $name, $content, $priority);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Update record via API
        $recordData = [
            'name' => $name,
            'type' => $type,
            'content' => $content,
            'ttl' => $ttl,
        ];
        
        if ($priority !== null && in_array($type, ['MX', 'SRV'])) {
            $recordData['priority'] = $priority;
        }
        
        $apiResult = $this->apiClient->updateRecord($record->api_zone_id, $record->record_id, $recordData);
        
        if ($apiResult === false) {
            return ['success' => false, 'message' => $this->apiClient->getLastError()];
        }
        
        // Update in local database
        Capsule::table('mod_whitednszone_records')
            ->where('id', $recordId)
            ->update([
                'name' => $name,
                'type' => $type,
                'content' => $content,
                'ttl' => $ttl,
                'priority' => $priority,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        // Log action
        $this->logAudit($record->zone_id, 'record_update', "Updated $type record: $name");
        
        return ['success' => true, 'message' => 'Record updated successfully'];
    }

    /**
     * AJAX: Delete record
     */
    private function ajaxDeleteRecord()
    {
        $recordId = (int)($_POST['record_id'] ?? 0);
        
        // Get record and validate access
        $record = Capsule::table('mod_whitednszone_records')
            ->join('mod_whitednszone_zones', 'mod_whitednszone_zones.id', '=', 'mod_whitednszone_records.zone_id')
            ->where('mod_whitednszone_records.id', $recordId)
            ->where('mod_whitednszone_zones.userid', $this->userId)
            ->select('mod_whitednszone_records.*', 'mod_whitednszone_zones.zone_id as api_zone_id')
            ->first();
        
        if (!$record) {
            return ['success' => false, 'message' => 'Record not found'];
        }
        
        // Delete record via API
        $apiResult = $this->apiClient->deleteRecord($record->api_zone_id, $record->record_id);
        
        if ($apiResult === false) {
            // Log but continue with local deletion
            error_log('WhiteDNSZone API delete failed: ' . $this->apiClient->getLastError());
        }
        
        // Delete from local database
        Capsule::table('mod_whitednszone_records')
            ->where('id', $recordId)
            ->delete();
        
        // Log action
        $this->logAudit($record->zone_id, 'record_delete', "Deleted {$record->type} record: {$record->name}");
        
        return ['success' => true, 'message' => 'Record deleted successfully'];
    }

    /**
     * AJAX: Bulk delete records
     */
    private function ajaxBulkDelete()
    {
        $recordIds = $_POST['record_ids'] ?? [];
        
        if (empty($recordIds) || !is_array($recordIds)) {
            return ['success' => false, 'message' => 'No records selected'];
        }
        
        $deleted = 0;
        foreach ($recordIds as $recordId) {
            $result = $this->ajaxDeleteRecord();
            if ($result['success']) {
                $deleted++;
            }
        }
        
        return ['success' => true, 'message' => "Deleted $deleted record(s) successfully"];
    }

    /**
     * AJAX: Apply template
     */
    private function ajaxApplyTemplate()
    {
        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $templateId = (int)($_POST['template_id'] ?? 0);
        
        // Validate zone access
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return ['success' => false, 'message' => 'Zone not found'];
        }
        
        // Get template
        $template = Capsule::table('mod_whitednszone_templates')
            ->where('id', $templateId)
            ->first();
        
        if (!$template) {
            return ['success' => false, 'message' => 'Template not found'];
        }
        
        $records = json_decode($template->records, true);
        $created = 0;
        
        foreach ($records as $record) {
            // Replace placeholders
            $record['content'] = str_replace('{domain}', $zone->domain, $record['content']);
            
            // Skip if placeholder still exists
            if (strpos($record['content'], '{') !== false) {
                continue;
            }
            
            $_POST = array_merge($_POST, [
                'zone_id' => $zoneId,
                'name' => $record['name'],
                'type' => $record['type'],
                'content' => $record['content'],
                'ttl' => $record['ttl'] ?? 3600,
                'priority' => $record['priority'] ?? null,
            ]);
            
            $result = $this->ajaxAddRecord();
            if ($result['success']) {
                $created++;
            }
        }
        
        return ['success' => true, 'message' => "Applied template, created $created record(s)"];
    }

    /**
     * AJAX: Check propagation
     */
    private function ajaxCheckPropagation()
    {
        $domain = $_REQUEST['domain'] ?? '';
        $type = $_REQUEST['type'] ?? 'A';
        
        if (empty($domain)) {
            return ['success' => false, 'message' => 'Domain is required'];
        }
        
        $result = $this->apiClient->checkPropagation($domain, $type);
        
        if ($result === false) {
            return ['success' => false, 'message' => $this->apiClient->getLastError()];
        }
        
        return ['success' => true, 'data' => $result];
    }

    /**
     * AJAX: Get DNSSEC
     */
    private function ajaxGetDNSSEC()
    {
        $zoneId = (int)($_REQUEST['zone_id'] ?? 0);
        
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return ['success' => false, 'message' => 'Zone not found'];
        }
        
        $result = $this->apiClient->getDNSSEC($zone->zone_id);
        
        if ($result === false) {
            return ['success' => false, 'message' => $this->apiClient->getLastError()];
        }
        
        return ['success' => true, 'data' => $result];
    }

    /**
     * AJAX: Toggle DNSSEC
     */
    private function ajaxToggleDNSSEC()
    {
        $zoneId = (int)($_POST['zone_id'] ?? 0);
        $enable = $_POST['enable'] === 'true';
        
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return ['success' => false, 'message' => 'Zone not found'];
        }
        
        if ($enable) {
            $result = $this->apiClient->enableDNSSEC($zone->zone_id);
        } else {
            $result = $this->apiClient->disableDNSSEC($zone->zone_id);
        }
        
        if ($result === false) {
            return ['success' => false, 'message' => $this->apiClient->getLastError()];
        }
        
        $this->logAudit($zoneId, 'dnssec_' . ($enable ? 'enable' : 'disable'), 
            'DNSSEC ' . ($enable ? 'enabled' : 'disabled'));
        
        return ['success' => true, 'message' => 'DNSSEC ' . ($enable ? 'enabled' : 'disabled') . ' successfully'];
    }

    /**
     * AJAX: Get audit log
     */
    private function ajaxGetAuditLog()
    {
        $zoneId = (int)($_REQUEST['zone_id'] ?? 0);
        
        $zone = Capsule::table('mod_whitednszone_zones')
            ->where('id', $zoneId)
            ->where('userid', $this->userId)
            ->first();
        
        if (!$zone) {
            return ['success' => false, 'message' => 'Zone not found'];
        }
        
        $logs = Capsule::table('mod_whitednszone_audit')
            ->where('zone_id', $zoneId)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
        
        return ['success' => true, 'logs' => $logs];
    }

    /**
     * AJAX: Validate record
     */
    private function ajaxValidateRecord()
    {
        $type = $_POST['type'] ?? '';
        $name = $_POST['name'] ?? '';
        $content = $_POST['content'] ?? '';
        $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : null;
        
        $validation = $this->validateRecord($type, $name, $content, $priority);
        
        return $validation;
    }

    /**
     * Validate DNS record
     */
    private function validateRecord($type, $name, $content, $priority = null)
    {
        $result = ['valid' => true, 'message' => '', 'suggestions' => []];
        
        // Validate type
        $validTypes = ['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'NS', 'SRV', 'CAA', 'PTR'];
        if (!in_array($type, $validTypes)) {
            $result['valid'] = false;
            $result['message'] = 'Invalid record type';
            return $result;
        }
        
        // Validate based on type
        switch ($type) {
            case 'A':
                if (!filter_var($content, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $result['valid'] = false;
                    $result['message'] = 'Invalid IPv4 address';
                }
                break;
                
            case 'AAAA':
                if (!filter_var($content, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    $result['valid'] = false;
                    $result['message'] = 'Invalid IPv6 address';
                }
                break;
                
            case 'CNAME':
                if (empty($content)) {
                    $result['valid'] = false;
                    $result['message'] = 'CNAME target cannot be empty';
                }
                if ($name === '@') {
                    $result['suggestions'][] = 'CNAME records cannot be created for the root domain (@)';
                }
                break;
                
            case 'MX':
                if ($priority === null) {
                    $result['valid'] = false;
                    $result['message'] = 'MX records require a priority value';
                }
                if (empty($content)) {
                    $result['valid'] = false;
                    $result['message'] = 'MX target cannot be empty';
                }
                break;
                
            case 'TXT':
                if (strlen($content) > 255) {
                    $result['suggestions'][] = 'TXT records longer than 255 characters may need to be split';
                }
                break;
        }
        
        return $result;
    }

    /**
     * Log audit action
     */
    private function logAudit($zoneId, $action, $details)
    {
        if (!($this->vars['enable_audit_log'] ?? false)) {
            return;
        }
        
        Capsule::table('mod_whitednszone_audit')->insert([
            'userid' => $this->userId,
            'zone_id' => $zoneId,
            'action' => $action,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
