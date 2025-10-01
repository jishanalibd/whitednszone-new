<!DOCTYPE html>
<html>
<head>
    <style>
        .nav-tabs {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>WhiteDNSZone Management</h2>

<ul class="nav nav-tabs">
    <li><a href="?module=whitednszone&action=dashboard">Dashboard</a></li>
    <li class="active"><a href="?module=whitednszone&action=zones">All Zones</a></li>
    <li><a href="?module=whitednszone&action=records">All Records</a></li>
    <li><a href="?module=whitednszone&action=logs">Audit Logs</a></li>
</ul>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">All DNS Zones</h3>
    </div>
    <div class="panel-body">
        <?php if (empty($zones)): ?>
            <p>No zones found.</p>
        <?php else: ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Domain</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($zones as $zone): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($zone->id); ?></td>
                            <td><strong><?php echo htmlspecialchars($zone->domain); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($zone->firstname . ' ' . $zone->lastname); ?><br>
                                <small><?php echo htmlspecialchars($zone->email); ?></small>
                            </td>
                            <td>
                                <?php if ($zone->status === 'active'): ?>
                                    <span class="label label-success">Active</span>
                                <?php else: ?>
                                    <span class="label label-default"><?php echo ucfirst(htmlspecialchars($zone->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($zone->created_at)); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($zone->updated_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <a href="?module=whitednszone&action=zones&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
