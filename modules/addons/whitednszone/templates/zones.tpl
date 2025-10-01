<link rel="stylesheet" href="../modules/addons/whitednszone/assets/css/style.css">

<div class="container whitednszone-container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-globe"></i> DNS Zone Management
                    </h3>
                </div>
                <div class="panel-body">
                    <p>Manage your DNS zones and records for all your domains.</p>
                    
                    {if $zones}
                        <!-- Search and Filter Bar -->
                        <div class="whitednszone-search-bar">
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="text" id="zoneSearch" class="form-control" placeholder="Search domains...">
                                </div>
                                <div class="col-sm-3">
                                    <select id="zoneStatusFilter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select id="zonesPerPage" class="form-control">
                                        <option value="10">10 per page</option>
                                        <option value="25">25 per page</option>
                                        <option value="50">50 per page</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" id="clearFilters" class="btn btn-default">
                                        <i class="fa fa-times"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="zonesStats" class="text-muted" style="margin-bottom: 10px;"></div>
                        
                        <div class="table-responsive whitednszone-zones-table">
                            <table class="table table-striped table-hover" id="zonesTable">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Status</th>
                                        <th>Records</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$zones item=zone}
                                        <tr>
                                            <td>
                                                <strong>{$zone->domain}</strong>
                                            </td>
                                            <td>
                                                {if $zone->status eq 'active'}
                                                    <span class="label label-success">Active</span>
                                                {else}
                                                    <span class="label label-default">{$zone->status|ucfirst}</span>
                                                {/if}
                                            </td>
                                            <td>
                                                <span class="badge">
                                                    {assign var="recordCount" value=0}
                                                    {* This would be populated from database *}
                                                    0
                                                </span>
                                            </td>
                                            <td>{$zone->created_at|date_format:"%Y-%m-%d"}</td>
                                            <td>
                                                <a href="{$modulelink}&action=manage&zone_id={$zone->id}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-cog"></i> Manage
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="zonesPagination" class="text-center whitednszone-pagination"></div>
                    {else}
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No DNS zones found. Contact support to add a zone.
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../modules/addons/whitednszone/assets/js/zones.js"></script>
