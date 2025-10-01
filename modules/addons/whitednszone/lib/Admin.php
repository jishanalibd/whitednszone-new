<?php

namespace WhiteDNSZone;

use WHMCS\Database\Capsule;

/**
 * Admin Area Handler
 */
class Admin
{
    private $vars;

    public function __construct($vars)
    {
        $this->vars = $vars;
    }

    /**
     * Main output handler
     *
     * @return string
     */
    public function output()
    {
        $action = $_REQUEST['action'] ?? 'dashboard';
        
        switch ($action) {
            case 'zones':
                return $this->viewZones();
            case 'records':
                return $this->viewRecords();
            case 'logs':
                return $this->viewLogs();
            default:
                return $this->dashboard();
        }
    }

    /**
     * Dashboard
     */
    private function dashboard()
    {
        // Get statistics
        $stats = [
            'total_zones' => Capsule::table('mod_whitednszone_zones')->count(),
            'total_records' => Capsule::table('mod_whitednszone_records')->count(),
            'total_users' => Capsule::table('mod_whitednszone_zones')
                ->distinct('userid')
                ->count('userid'),
            'recent_changes' => Capsule::table('mod_whitednszone_audit')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];
        
        // Get zone distribution by user
        $zonesByUser = Capsule::table('mod_whitednszone_zones')
            ->select('userid', Capsule::raw('COUNT(*) as zone_count'))
            ->groupBy('userid')
            ->orderBy('zone_count', 'desc')
            ->limit(10)
            ->get();
        
        // Get record type distribution
        $recordsByType = Capsule::table('mod_whitednszone_records')
            ->select('type', Capsule::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
        
        ob_start();
        include __DIR__ . '/../templates/admin/dashboard.php';
        return ob_get_clean();
    }

    /**
     * View all zones
     */
    private function viewZones()
    {
        $page = (int)($_REQUEST['page'] ?? 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        $zones = Capsule::table('mod_whitednszone_zones')
            ->join('tblclients', 'tblclients.id', '=', 'mod_whitednszone_zones.userid')
            ->select(
                'mod_whitednszone_zones.*',
                'tblclients.firstname',
                'tblclients.lastname',
                'tblclients.email'
            )
            ->orderBy('mod_whitednszone_zones.created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
        
        $totalZones = Capsule::table('mod_whitednszone_zones')->count();
        $totalPages = ceil($totalZones / $perPage);
        
        ob_start();
        include __DIR__ . '/../templates/admin/zones.php';
        return ob_get_clean();
    }

    /**
     * View all records
     */
    private function viewRecords()
    {
        $page = (int)($_REQUEST['page'] ?? 1);
        $perPage = 100;
        $offset = ($page - 1) * $perPage;
        
        $records = Capsule::table('mod_whitednszone_records')
            ->join('mod_whitednszone_zones', 'mod_whitednszone_zones.id', '=', 'mod_whitednszone_records.zone_id')
            ->select(
                'mod_whitednszone_records.*',
                'mod_whitednszone_zones.domain',
                'mod_whitednszone_zones.userid'
            )
            ->orderBy('mod_whitednszone_records.updated_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
        
        $totalRecords = Capsule::table('mod_whitednszone_records')->count();
        $totalPages = ceil($totalRecords / $perPage);
        
        ob_start();
        include __DIR__ . '/../templates/admin/records.php';
        return ob_get_clean();
    }

    /**
     * View audit logs
     */
    private function viewLogs()
    {
        $page = (int)($_REQUEST['page'] ?? 1);
        $perPage = 100;
        $offset = ($page - 1) * $perPage;
        
        $logs = Capsule::table('mod_whitednszone_audit')
            ->join('tblclients', 'tblclients.id', '=', 'mod_whitednszone_audit.userid')
            ->leftJoin('mod_whitednszone_zones', 'mod_whitednszone_zones.id', '=', 'mod_whitednszone_audit.zone_id')
            ->select(
                'mod_whitednszone_audit.*',
                'tblclients.firstname',
                'tblclients.lastname',
                'tblclients.email',
                'mod_whitednszone_zones.domain'
            )
            ->orderBy('mod_whitednszone_audit.created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
        
        $totalLogs = Capsule::table('mod_whitednszone_audit')->count();
        $totalPages = ceil($totalLogs / $perPage);
        
        ob_start();
        include __DIR__ . '/../templates/admin/logs.php';
        return ob_get_clean();
    }
}
