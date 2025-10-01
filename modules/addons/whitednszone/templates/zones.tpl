<div class="container">
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
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

<style>
.table > tbody > tr > td {
    vertical-align: middle;
}
</style>
