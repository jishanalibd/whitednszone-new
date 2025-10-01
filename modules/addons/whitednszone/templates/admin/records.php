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
        <!-- Search and Filter Form -->
        <form method="get" class="form-inline" style="margin-bottom: 20px;">
            <input type="hidden" name="module" value="whitednszone">
            <input type="hidden" name="action" value="records">
            
            <div class="form-group">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" class="form-control" 
                       placeholder="Name, Content, or Domain" 
                       value="<?php echo htmlspecialchars($_REQUEST['search'] ?? ''); ?>"
                       style="width: 250px;">
            </div>
            
            <div class="form-group" style="margin-left: 10px;">
                <label for="filter_type">Type:</label>
                <select name="filter_type" id="filter_type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach ($recordTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"
                                <?php echo (isset($_REQUEST['filter_type']) && $_REQUEST['filter_type'] === $type) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-left: 10px;">
                <label for="filter_domain">Domain:</label>
                <select name="filter_domain" id="filter_domain" class="form-control" style="width: 200px;">
                    <option value="">All Domains</option>
                    <?php foreach ($domains as $domain): ?>
                        <option value="<?php echo htmlspecialchars($domain); ?>"
                                <?php echo (isset($_REQUEST['filter_domain']) && $_REQUEST['filter_domain'] === $domain) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($domain); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                <i class="fa fa-search"></i> Filter
            </button>
            
            <?php if (!empty($_REQUEST['search']) || !empty($_REQUEST['filter_type']) || !empty($_REQUEST['filter_domain'])): ?>
                <a href="?module=whitednszone&action=records" class="btn btn-default" style="margin-left: 5px;">
                    <i class="fa fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>
        
        <?php if (!empty($_REQUEST['search']) || !empty($_REQUEST['filter_type']) || !empty($_REQUEST['filter_domain'])): ?>
            <div class="alert alert-info">
                Showing filtered results. 
                <?php if (!empty($_REQUEST['search'])): ?>
                    Search: <strong><?php echo htmlspecialchars($_REQUEST['search']); ?></strong>
                <?php endif; ?>
                <?php if (!empty($_REQUEST['filter_type'])): ?>
                    Type: <strong><?php echo htmlspecialchars($_REQUEST['filter_type']); ?></strong>
                <?php endif; ?>
                <?php if (!empty($_REQUEST['filter_domain'])): ?>
                    Domain: <strong><?php echo htmlspecialchars($_REQUEST['filter_domain']); ?></strong>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
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
                        <?php 
                        // Build query string for pagination
                        $queryParams = [
                            'module' => 'whitednszone',
                            'action' => 'records',
                        ];
                        if (!empty($_REQUEST['search'])) {
                            $queryParams['search'] = $_REQUEST['search'];
                        }
                        if (!empty($_REQUEST['filter_type'])) {
                            $queryParams['filter_type'] = $_REQUEST['filter_type'];
                        }
                        if (!empty($_REQUEST['filter_domain'])) {
                            $queryParams['filter_domain'] = $_REQUEST['filter_domain'];
                        }
                        
                        for ($i = 1; $i <= $totalPages; $i++): 
                            $queryParams['page'] = $i;
                            $queryString = http_build_query($queryParams);
                        ?>
                            <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <a href="?<?php echo $queryString; ?>">
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
