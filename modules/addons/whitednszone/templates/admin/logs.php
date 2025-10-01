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
    <li><a href="?module=whitednszone&action=zones">All Zones</a></li>
    <li><a href="?module=whitednszone&action=records">All Records</a></li>
    <li class="active"><a href="?module=whitednszone&action=logs">Audit Logs</a></li>
</ul>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Audit Logs</h3>
    </div>
    <div class="panel-body">
        <?php if (empty($logs)): ?>
            <p>No audit logs found.</p>
        <?php else: ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Domain</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?></td>
                            <td>
                                <?php echo htmlspecialchars($log->firstname . ' ' . $log->lastname); ?><br>
                                <small><?php echo htmlspecialchars($log->email); ?></small>
                            </td>
                            <td><?php echo $log->domain ? htmlspecialchars($log->domain) : '-'; ?></td>
                            <td><span class="label label-primary"><?php echo htmlspecialchars($log->action); ?></span></td>
                            <td><?php echo htmlspecialchars($log->details); ?></td>
                            <td><?php echo htmlspecialchars($log->ip_address); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <a href="?module=whitednszone&action=logs&page=<?php echo $i; ?>">
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
