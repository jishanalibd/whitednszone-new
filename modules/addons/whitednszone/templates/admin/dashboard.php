<!DOCTYPE html>
<html>
<head>
    <style>
        .stat-box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0 0 10px 0;
            font-size: 36px;
            color: #337ab7;
        }
        .stat-box p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }
        .nav-tabs {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>WhiteDNSZone Dashboard</h2>

<div class="row">
    <div class="col-md-3">
        <div class="stat-box">
            <h3><?php echo $stats['total_zones']; ?></h3>
            <p>Total Zones</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h3><?php echo $stats['total_records']; ?></h3>
            <p>Total Records</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h3><?php echo $stats['total_users']; ?></h3>
            <p>Active Users</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h3><?php echo number_format($stats['total_zones'] > 0 ? $stats['total_records'] / $stats['total_zones'] : 0, 1); ?></h3>
            <p>Avg Records/Zone</p>
        </div>
    </div>
</div>

<ul class="nav nav-tabs">
    <li class="active"><a href="?module=whitednszone&action=dashboard">Dashboard</a></li>
    <li><a href="?module=whitednszone&action=zones">All Zones</a></li>
    <li><a href="?module=whitednszone&action=records">All Records</a></li>
    <li><a href="?module=whitednszone&action=logs">Audit Logs</a></li>
</ul>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Changes</h3>
    </div>
    <div class="panel-body">
        <?php if (empty($stats['recent_changes'])): ?>
            <p>No recent changes found.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User ID</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_changes'] as $change): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($change->created_at); ?></td>
                            <td><?php echo htmlspecialchars($change->userid); ?></td>
                            <td><span class="label label-primary"><?php echo htmlspecialchars($change->action); ?></span></td>
                            <td><?php echo htmlspecialchars($change->details); ?></td>
                            <td><?php echo htmlspecialchars($change->ip_address); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Top Users by Zones</h3>
            </div>
            <div class="panel-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Zone Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zonesByUser as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user->userid); ?></td>
                                <td><?php echo htmlspecialchars($user->zone_count); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Record Type Distribution</h3>
            </div>
            <div class="panel-body">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recordsByType as $record): ?>
                            <tr>
                                <td><span class="label label-info"><?php echo htmlspecialchars($record->type); ?></span></td>
                                <td><?php echo htmlspecialchars($record->count); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
