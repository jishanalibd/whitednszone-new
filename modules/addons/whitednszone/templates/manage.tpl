<link rel="stylesheet" href="../modules/addons/whitednszone/assets/css/style.css">

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
                            
                            <!-- Search and Filter Bar -->
                            <div class="whitednszone-search-bar">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <input type="text" id="recordSearch" class="form-control" placeholder="Search records...">
                                    </div>
                                    <div class="col-sm-3">
                                        <select id="recordTypeFilter" class="form-control">
                                            <option value="">All Types</option>
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
                                    <div class="col-sm-2">
                                        <select id="recordsPerPage" class="form-control">
                                            <option value="10">10 per page</option>
                                            <option value="25">25 per page</option>
                                            <option value="50">50 per page</option>
                                            <option value="100">100 per page</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="recordsContainer">
                                <div class="loading-spinner">
                                    <i class="fa fa-spinner fa-spin"></i>
                                    <p>Loading records...</p>
                                </div>
                            </div>
                            
                            <div id="recordsPagination"></div>
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

<script src="../modules/addons/whitednszone/assets/js/client.js"></script>
<script>
// Initialize WhiteDNSZone module
$(document).ready(function() {
    whiteDNSZone.init({$zone_id}, '{$modulelink}');
});
</script>
