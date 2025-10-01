<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h2>
                    <i class="fa fa-globe"></i> {$zone->domain}
                    <small>DNS Management</small>
                </h2>
            </div>

            <ul class="nav nav-tabs" role="tablist" id="dnsManagementTabs">
                <li role="presentation" class="active">
                    <a href="#records" aria-controls="records" role="tab" data-toggle="tab">
                        <i class="fa fa-list"></i> DNS Records
                    </a>
                </li>
                <li role="presentation">
                    <a href="#templates" aria-controls="templates" role="tab" data-toggle="tab">
                        <i class="fa fa-file-text"></i> Templates
                    </a>
                </li>
                <li role="presentation">
                    <a href="#dnssec" aria-controls="dnssec" role="tab" data-toggle="tab">
                        <i class="fa fa-shield"></i> DNSSEC
                    </a>
                </li>
                <li role="presentation">
                    <a href="#audit" aria-controls="audit" role="tab" data-toggle="tab">
                        <i class="fa fa-history"></i> Audit Log
                    </a>
                </li>
                {if $enable_propagation_check}
                <li role="presentation">
                    <a href="#propagation" aria-controls="propagation" role="tab" data-toggle="tab">
                        <i class="fa fa-refresh"></i> Propagation Check
                    </a>
                </li>
                {/if}
            </ul>

            <div class="tab-content" style="margin-top: 20px;">
                <!-- DNS Records Tab -->
                <div role="tabpanel" class="tab-panel active" id="records">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 class="panel-title">DNS Records</h4>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <button class="btn btn-primary btn-sm" onclick="showAddRecordModal()">
                                        <i class="fa fa-plus"></i> Add Record
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="bulkDeleteRecords()" id="bulkDeleteBtn" style="display: none;">
                                        <i class="fa fa-trash"></i> Delete Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Nameservers:</strong> {$default_ns1}, {$default_ns2}
                            </div>
                            
                            <div id="recordsContainer">
                                <div class="text-center">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                    <p>Loading records...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Templates Tab -->
                <div role="tabpanel" class="tab-panel" id="templates">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">DNS Templates & Presets</h4>
                        </div>
                        <div class="panel-body">
                            <p>Apply pre-configured DNS record templates for common services.</p>
                            
                            <div class="row">
                                {foreach from=$templates item=template}
                                    <div class="col-md-4">
                                        <div class="panel panel-info">
                                            <div class="panel-heading">
                                                <h5 class="panel-title">{$template->name}</h5>
                                            </div>
                                            <div class="panel-body">
                                                <p>{$template->description}</p>
                                                <button class="btn btn-primary btn-block" onclick="applyTemplate({$template->id})">
                                                    <i class="fa fa-check"></i> Apply Template
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DNSSEC Tab -->
                <div role="tabpanel" class="tab-panel" id="dnssec">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">DNSSEC Management</h4>
                        </div>
                        <div class="panel-body">
                            <div id="dnssecContainer">
                                <div class="text-center">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                    <p>Loading DNSSEC information...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Log Tab -->
                <div role="tabpanel" class="tab-panel" id="audit">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Audit Log</h4>
                        </div>
                        <div class="panel-body">
                            <div id="auditContainer">
                                <div class="text-center">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                    <p>Loading audit log...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Propagation Check Tab -->
                {if $enable_propagation_check}
                <div role="tabpanel" class="tab-panel" id="propagation">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">DNS Propagation Check</h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Domain:</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="propDomain" value="{$zone->domain}">
                                    </div>
                                    <label class="col-sm-2 control-label">Record Type:</label>
                                    <div class="col-sm-2">
                                        <select class="form-control" id="propType">
                                            <option value="A">A</option>
                                            <option value="AAAA">AAAA</option>
                                            <option value="CNAME">CNAME</option>
                                            <option value="MX">MX</option>
                                            <option value="TXT">TXT</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-primary" onclick="checkPropagation()">
                                            <i class="fa fa-refresh"></i> Check
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="propagationResults" style="display: none;">
                                <hr>
                                <div id="propagationData"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Record Modal -->
<div class="modal fade" id="recordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" id="recordModalTitle">Add DNS Record</h4>
            </div>
            <div class="modal-body">
                <form id="recordForm" class="form-horizontal">
                    <input type="hidden" id="recordId" name="record_id">
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Type:</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="recordType" name="type" required onchange="handleTypeChange()">
                                <option value="A">A</option>
                                <option value="AAAA">AAAA</option>
                                <option value="CNAME">CNAME</option>
                                <option value="MX">MX</option>
                                <option value="TXT">TXT</option>
                                <option value="NS">NS</option>
                                <option value="SRV">SRV</option>
                                <option value="CAA">CAA</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="recordName" name="name" 
                                   placeholder="@ or subdomain" required>
                            <small class="help-block">Use @ for root domain, or enter subdomain name</small>
                        </div>
                    </div>
                    
                    <div class="form-group" id="priorityGroup" style="display: none;">
                        <label class="col-sm-3 control-label">Priority:</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="recordPriority" name="priority" min="0">
                            <small class="help-block">Lower values have higher priority</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Content:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="recordContent" name="content" required>
                            <small class="help-block" id="contentHelp">Enter the target value</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">TTL:</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="recordTTL" name="ttl">
                                <option value="300">5 minutes (300)</option>
                                <option value="600">10 minutes (600)</option>
                                <option value="1800">30 minutes (1800)</option>
                                <option value="3600" selected>1 hour (3600)</option>
                                <option value="7200">2 hours (7200)</option>
                                <option value="14400">4 hours (14400)</option>
                                <option value="43200">12 hours (43200)</option>
                                <option value="86400">24 hours (86400)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="validationMessages"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveRecord()">Save Record</button>
            </div>
        </div>
    </div>
</div>

<script>
var zoneId = {$zone_id};
var moduleLink = '{$modulelink}';

// Load records on page load
$(document).ready(function() {
    loadRecords();
    
    // Load data when tabs are shown
    $('#dnsManagementTabs a[href="#dnssec"]').on('shown.bs.tab', function() {
        loadDNSSEC();
    });
    
    $('#dnsManagementTabs a[href="#audit"]').on('shown.bs.tab', function() {
        loadAuditLog();
    });
});

// Load DNS records
function loadRecords() {
    $.ajax({
        url: moduleLink,
        type: 'GET',
        data: {
            ajax: 1,
            ajax_action: 'get_records',
            zone_id: zoneId
        },
        success: function(response) {
            if (response.success) {
                displayRecords(response.records);
            } else {
                $('#recordsContainer').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function() {
            $('#recordsContainer').html('<div class="alert alert-danger">Failed to load records</div>');
        }
    });
}

// Display records
function displayRecords(records) {
    if (records.length === 0) {
        $('#recordsContainer').html('<div class="alert alert-info">No DNS records found. Add your first record!</div>');
        return;
    }
    
    var html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>';
    html += '<th>Type</th><th>Name</th><th>Content</th><th>TTL</th><th>Priority</th><th>Actions</th>';
    html += '</tr></thead><tbody>';
    
    records.forEach(function(record) {
        html += '<tr>';
        html += '<td><input type="checkbox" class="record-checkbox" value="' + record.id + '" onchange="updateBulkDeleteButton()"></td>';
        html += '<td><span class="label label-info">' + record.type + '</span></td>';
        html += '<td>' + htmlEscape(record.name) + '</td>';
        html += '<td>' + htmlEscape(record.content) + '</td>';
        html += '<td>' + record.ttl + '</td>';
        html += '<td>' + (record.priority || '-') + '</td>';
        html += '<td>';
        html += '<button class="btn btn-xs btn-primary" onclick="editRecord(' + record.id + ')"><i class="fa fa-edit"></i></button> ';
        html += '<button class="btn btn-xs btn-danger" onclick="deleteRecord(' + record.id + ')"><i class="fa fa-trash"></i></button>';
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    $('#recordsContainer').html(html);
}

// Show add record modal
function showAddRecordModal() {
    $('#recordModalTitle').text('Add DNS Record');
    $('#recordForm')[0].reset();
    $('#recordId').val('');
    $('#recordModal').modal('show');
}

// Handle record type change
function handleTypeChange() {
    var type = $('#recordType').val();
    var contentHelp = '';
    
    // Show/hide priority field
    if (type === 'MX' || type === 'SRV') {
        $('#priorityGroup').show();
        $('#recordPriority').attr('required', true);
    } else {
        $('#priorityGroup').hide();
        $('#recordPriority').attr('required', false);
    }
    
    // Update content help text
    switch (type) {
        case 'A':
            contentHelp = 'Enter an IPv4 address (e.g., 192.0.2.1)';
            break;
        case 'AAAA':
            contentHelp = 'Enter an IPv6 address (e.g., 2001:0db8::1)';
            break;
        case 'CNAME':
            contentHelp = 'Enter a domain name (e.g., example.com)';
            break;
        case 'MX':
            contentHelp = 'Enter a mail server (e.g., mail.example.com)';
            break;
        case 'TXT':
            contentHelp = 'Enter text value (e.g., v=spf1 include:_spf.example.com ~all)';
            break;
    }
    
    $('#contentHelp').text(contentHelp);
}

// Save record
function saveRecord() {
    var formData = {
        ajax: 1,
        zone_id: zoneId,
        record_id: $('#recordId').val(),
        type: $('#recordType').val(),
        name: $('#recordName').val(),
        content: $('#recordContent').val(),
        ttl: $('#recordTTL').val(),
        priority: $('#recordPriority').val()
    };
    
    var action = formData.record_id ? 'update_record' : 'add_record';
    formData.ajax_action = action;
    
    $.ajax({
        url: moduleLink,
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#recordModal').modal('hide');
                loadRecords();
                showAlert('success', response.message);
            } else {
                $('#validationMessages').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function() {
            $('#validationMessages').html('<div class="alert alert-danger">Request failed</div>');
        }
    });
}

// Edit record
function editRecord(recordId) {
    // Find record in current data and populate form
    $('#recordModalTitle').text('Edit DNS Record');
    $('#recordId').val(recordId);
    // Load record details via AJAX and populate form
    $('#recordModal').modal('show');
}

// Delete record
function deleteRecord(recordId) {
    if (!confirm('Are you sure you want to delete this record?')) {
        return;
    }
    
    $.ajax({
        url: moduleLink,
        type: 'POST',
        data: {
            ajax: 1,
            ajax_action: 'delete_record',
            record_id: recordId
        },
        success: function(response) {
            if (response.success) {
                loadRecords();
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        }
    });
}

// Bulk delete records
function bulkDeleteRecords() {
    var selectedIds = [];
    $('.record-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (selectedIds.length === 0) {
        return;
    }
    
    if (!confirm('Delete ' + selectedIds.length + ' record(s)?')) {
        return;
    }
    
    $.ajax({
        url: moduleLink,
        type: 'POST',
        data: {
            ajax: 1,
            ajax_action: 'bulk_delete',
            record_ids: selectedIds
        },
        success: function(response) {
            if (response.success) {
                loadRecords();
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        }
    });
}

// Toggle select all
function toggleSelectAll() {
    var checked = $('#selectAll').prop('checked');
    $('.record-checkbox').prop('checked', checked);
    updateBulkDeleteButton();
}

// Update bulk delete button
function updateBulkDeleteButton() {
    var anyChecked = $('.record-checkbox:checked').length > 0;
    $('#bulkDeleteBtn').toggle(anyChecked);
}

// Apply template
function applyTemplate(templateId) {
    if (!confirm('This will add DNS records from the template. Continue?')) {
        return;
    }
    
    $.ajax({
        url: moduleLink,
        type: 'POST',
        data: {
            ajax: 1,
            ajax_action: 'apply_template',
            zone_id: zoneId,
            template_id: templateId
        },
        success: function(response) {
            if (response.success) {
                loadRecords();
                showAlert('success', response.message);
                // Switch to records tab
                $('#dnsManagementTabs a[href="#records"]').tab('show');
            } else {
                showAlert('danger', response.message);
            }
        }
    });
}

// Load DNSSEC info
function loadDNSSEC() {
    $.ajax({
        url: moduleLink,
        type: 'GET',
        data: {
            ajax: 1,
            ajax_action: 'get_dnssec',
            zone_id: zoneId
        },
        success: function(response) {
            if (response.success) {
                displayDNSSEC(response.data);
            } else {
                $('#dnssecContainer').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        }
    });
}

// Display DNSSEC info
function displayDNSSEC(data) {
    var html = '<div class="form-horizontal">';
    html += '<div class="form-group">';
    html += '<label class="col-sm-3 control-label">DNSSEC Status:</label>';
    html += '<div class="col-sm-9">';
    
    if (data && data.enabled) {
        html += '<span class="label label-success">Enabled</span>';
        html += '<button class="btn btn-danger btn-sm" style="margin-left: 10px;" onclick="toggleDNSSEC(false)">Disable DNSSEC</button>';
        
        if (data.ds_records) {
            html += '<hr><h5>DS Records</h5>';
            html += '<p class="help-block">Add these DS records to your domain registrar:</p>';
            html += '<pre>' + JSON.stringify(data.ds_records, null, 2) + '</pre>';
        }
    } else {
        html += '<span class="label label-default">Disabled</span>';
        html += '<button class="btn btn-success btn-sm" style="margin-left: 10px;" onclick="toggleDNSSEC(true)">Enable DNSSEC</button>';
    }
    
    html += '</div></div></div>';
    $('#dnssecContainer').html(html);
}

// Toggle DNSSEC
function toggleDNSSEC(enable) {
    var action = enable ? 'enable' : 'disable';
    if (!confirm('Are you sure you want to ' + action + ' DNSSEC?')) {
        return;
    }
    
    $.ajax({
        url: moduleLink,
        type: 'POST',
        data: {
            ajax: 1,
            ajax_action: 'toggle_dnssec',
            zone_id: zoneId,
            enable: enable
        },
        success: function(response) {
            if (response.success) {
                loadDNSSEC();
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        }
    });
}

// Load audit log
function loadAuditLog() {
    $.ajax({
        url: moduleLink,
        type: 'GET',
        data: {
            ajax: 1,
            ajax_action: 'get_audit_log',
            zone_id: zoneId
        },
        success: function(response) {
            if (response.success) {
                displayAuditLog(response.logs);
            } else {
                $('#auditContainer').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        }
    });
}

// Display audit log
function displayAuditLog(logs) {
    if (logs.length === 0) {
        $('#auditContainer').html('<div class="alert alert-info">No audit log entries found.</div>');
        return;
    }
    
    var html = '<div class="table-responsive"><table class="table table-striped">';
    html += '<thead><tr><th>Date</th><th>Action</th><th>Details</th><th>IP Address</th></tr></thead>';
    html += '<tbody>';
    
    logs.forEach(function(log) {
        html += '<tr>';
        html += '<td>' + log.created_at + '</td>';
        html += '<td><span class="label label-primary">' + log.action + '</span></td>';
        html += '<td>' + htmlEscape(log.details) + '</td>';
        html += '<td>' + log.ip_address + '</td>';
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    $('#auditContainer').html(html);
}

// Check propagation
function checkPropagation() {
    var domain = $('#propDomain').val();
    var type = $('#propType').val();
    
    $('#propagationResults').show();
    $('#propagationData').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Checking...</div>');
    
    $.ajax({
        url: moduleLink,
        type: 'GET',
        data: {
            ajax: 1,
            ajax_action: 'check_propagation',
            domain: domain,
            type: type
        },
        success: function(response) {
            if (response.success) {
                displayPropagation(response.data);
            } else {
                $('#propagationData').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        }
    });
}

// Display propagation results
function displayPropagation(data) {
    var html = '<div class="table-responsive"><table class="table table-striped">';
    html += '<thead><tr><th>Server</th><th>Location</th><th>Result</th><th>Status</th></tr></thead>';
    html += '<tbody>';
    
    if (data && data.servers) {
        data.servers.forEach(function(server) {
            html += '<tr>';
            html += '<td>' + server.name + '</td>';
            html += '<td>' + server.location + '</td>';
            html += '<td>' + (server.result || 'N/A') + '</td>';
            html += '<td>';
            if (server.success) {
                html += '<span class="label label-success">OK</span>';
            } else {
                html += '<span class="label label-danger">Failed</span>';
            }
            html += '</td></tr>';
        });
    } else {
        html += '<tr><td colspan="4">No data available</td></tr>';
    }
    
    html += '</tbody></table></div>';
    $('#propagationData').html(html);
}

// Show alert
function showAlert(type, message) {
    var alert = '<div class="alert alert-' + type + ' alert-dismissible" style="position: fixed; top: 70px; right: 20px; z-index: 9999;">';
    alert += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
    alert += message;
    alert += '</div>';
    $('body').append(alert);
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
}

// HTML escape
function htmlEscape(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}
</script>

<style>
.tab-content {
    padding: 20px 0;
}

.panel-title {
    margin: 0;
}

.table > tbody > tr > td {
    vertical-align: middle;
}

.label {
    font-size: 11px;
}

@media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .btn-group {
        display: block;
    }
    
    .btn-group .btn {
        display: block;
        width: 100%;
        margin-bottom: 5px;
    }
}
</style>
