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
    <li class="active"><a href="?module=whitednszone&action=records">All Records</a></li>
    <li><a href="?module=whitednszone&action=logs">Audit Logs</a></li>
</ul>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">All DNS Records</h3>
    </div>
    <div class="panel-body">
        <?php if (empty($records)): ?>
            <p>No records found.</p>
        <?php else: ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Domain</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Content</th>
                        <th>TTL</th>
                        <th>Priority</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record->id); ?></td>
                            <td><?php echo htmlspecialchars($record->domain); ?></td>
                            <td><span class="label label-info"><?php echo htmlspecialchars($record->type); ?></span></td>
                            <td><?php echo htmlspecialchars($record->name); ?></td>
                            <td><?php echo htmlspecialchars(substr($record->content, 0, 50)); ?><?php echo strlen($record->content) > 50 ? '...' : ''; ?></td>
                            <td><?php echo htmlspecialchars($record->ttl); ?></td>
                            <td><?php echo $record->priority ? htmlspecialchars($record->priority) : '-'; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($record->updated_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <a href="?module=whitednszone&action=records&page=<?php echo $i; ?>">
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
