/**
 * WhiteDNSZone WHMCS Module - Client Area JavaScript
 * Professional production-ready client-side functionality
 */

(function($) {
    'use strict';
    
    // Global variables
    var whiteDNSZone = {
        zoneId: null,
        moduleLink: null,
        allRecords: [],
        filteredRecords: [],
        currentPage: 1,
        recordsPerPage: 10,
        searchTerm: '',
        filterType: '',
        
        /**
         * Initialize the module
         */
        init: function(zoneId, moduleLink) {
            this.zoneId = zoneId;
            this.moduleLink = moduleLink;
            
            // Load records on page load
            this.loadRecords();
            
            // Setup tab handlers
            this.setupTabHandlers();
            
            // Setup search and filter
            this.setupSearchFilter();
        },
        
        /**
         * Setup tab handlers
         */
        setupTabHandlers: function() {
            var self = this;
            
            $('#dnsManagementTabs a[href="#dnssec"]').on('shown.bs.tab', function() {
                self.loadDNSSEC();
            });
            
            $('#dnsManagementTabs a[href="#audit"]').on('shown.bs.tab', function() {
                self.loadAuditLog();
            });
        },
        
        /**
         * Setup search and filter functionality
         */
        setupSearchFilter: function() {
            var self = this;
            
            // Search input
            $('#recordSearch').on('keyup', function() {
                self.searchTerm = $(this).val().toLowerCase();
                self.filterAndDisplayRecords();
            });
            
            // Type filter
            $('#recordTypeFilter').on('change', function() {
                self.filterType = $(this).val();
                self.filterAndDisplayRecords();
            });
            
            // Records per page
            $('#recordsPerPage').on('change', function() {
                self.recordsPerPage = parseInt($(this).val());
                self.currentPage = 1;
                self.filterAndDisplayRecords();
            });
        },
        
        /**
         * Load DNS records
         */
        loadRecords: function() {
            var self = this;
            
            $.ajax({
                url: self.moduleLink,
                type: 'GET',
                data: {
                    ajax: 1,
                    ajax_action: 'get_records',
                    zone_id: self.zoneId
                },
                success: function(response) {
                    if (response.success) {
                        self.allRecords = response.records || [];
                        self.filterAndDisplayRecords();
                    } else {
                        $('#recordsContainer').html('<div class="alert alert-danger">' + self.escapeHtml(response.message) + '</div>');
                    }
                },
                error: function() {
                    $('#recordsContainer').html('<div class="alert alert-danger">Failed to load records</div>');
                }
            });
        },
        
        /**
         * Filter and display records with pagination
         */
        filterAndDisplayRecords: function() {
            var self = this;
            
            // Filter records
            self.filteredRecords = self.allRecords.filter(function(record) {
                var matchesSearch = true;
                var matchesType = true;
                
                if (self.searchTerm) {
                    matchesSearch = (
                        record.name.toLowerCase().indexOf(self.searchTerm) !== -1 ||
                        record.content.toLowerCase().indexOf(self.searchTerm) !== -1 ||
                        record.type.toLowerCase().indexOf(self.searchTerm) !== -1
                    );
                }
                
                if (self.filterType) {
                    matchesType = record.type === self.filterType;
                }
                
                return matchesSearch && matchesType;
            });
            
            // Display records
            self.displayRecords();
            self.displayPagination();
        },
        
        /**
         * Display records
         */
        displayRecords: function() {
            var self = this;
            
            if (self.filteredRecords.length === 0) {
                var emptyMessage = 'No DNS records found.';
                if (self.searchTerm || self.filterType) {
                    emptyMessage = 'No records match your filter criteria.';
                }
                $('#recordsContainer').html(
                    '<div class="empty-state">' +
                    '<i class="fa fa-search"></i>' +
                    '<h4>' + emptyMessage + '</h4>' +
                    '<p>Try adjusting your search or filter criteria.</p>' +
                    '</div>'
                );
                return;
            }
            
            // Calculate pagination
            var start = (self.currentPage - 1) * self.recordsPerPage;
            var end = start + self.recordsPerPage;
            var pageRecords = self.filteredRecords.slice(start, end);
            
            // Build table
            var html = '<div class="table-responsive"><table class="table table-striped table-hover">';
            html += '<thead><tr>';
            html += '<th width="40"><input type="checkbox" id="selectAll" onchange="whiteDNSZone.toggleSelectAll()"></th>';
            html += '<th>Type</th><th>Name</th><th>Content</th><th>TTL</th><th>Priority</th><th width="120">Actions</th>';
            html += '</tr></thead><tbody>';
            
            pageRecords.forEach(function(record) {
                html += '<tr>';
                html += '<td><input type="checkbox" class="record-checkbox" value="' + record.id + '" onchange="whiteDNSZone.updateBulkDeleteButton()"></td>';
                html += '<td><span class="label label-info record-type-badge">' + self.escapeHtml(record.type) + '</span></td>';
                html += '<td>' + self.escapeHtml(record.name) + '</td>';
                html += '<td style="word-break: break-all; max-width: 300px;">' + self.escapeHtml(record.content) + '</td>';
                html += '<td>' + record.ttl + '</td>';
                html += '<td>' + (record.priority || '-') + '</td>';
                html += '<td>';
                html += '<button class="btn btn-xs btn-primary" onclick="whiteDNSZone.editRecord(' + record.id + ')" title="Edit"><i class="fa fa-edit"></i></button> ';
                html += '<button class="btn btn-xs btn-danger" onclick="whiteDNSZone.deleteRecord(' + record.id + ')" title="Delete"><i class="fa fa-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            $('#recordsContainer').html(html);
        },
        
        /**
         * Display pagination
         */
        displayPagination: function() {
            var self = this;
            var totalPages = Math.ceil(self.filteredRecords.length / self.recordsPerPage);
            
            if (totalPages <= 1) {
                $('#recordsPagination').html('');
                return;
            }
            
            var html = '<div class="whitednszone-pagination">';
            html += '<ul class="pagination">';
            
            // Previous button
            html += '<li' + (self.currentPage === 1 ? ' class="disabled"' : '') + '>';
            html += '<a href="#" onclick="whiteDNSZone.goToPage(' + (self.currentPage - 1) + '); return false;">&laquo;</a>';
            html += '</li>';
            
            // Page numbers
            var startPage = Math.max(1, self.currentPage - 2);
            var endPage = Math.min(totalPages, self.currentPage + 2);
            
            if (startPage > 1) {
                html += '<li><a href="#" onclick="whiteDNSZone.goToPage(1); return false;">1</a></li>';
                if (startPage > 2) {
                    html += '<li class="disabled"><a href="#">...</a></li>';
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                html += '<li' + (i === self.currentPage ? ' class="active"' : '') + '>';
                html += '<a href="#" onclick="whiteDNSZone.goToPage(' + i + '); return false;">' + i + '</a>';
                html += '</li>';
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<li class="disabled"><a href="#">...</a></li>';
                }
                html += '<li><a href="#" onclick="whiteDNSZone.goToPage(' + totalPages + '); return false;">' + totalPages + '</a></li>';
            }
            
            // Next button
            html += '<li' + (self.currentPage === totalPages ? ' class="disabled"' : '') + '>';
            html += '<a href="#" onclick="whiteDNSZone.goToPage(' + (self.currentPage + 1) + '); return false;">&raquo;</a>';
            html += '</li>';
            
            html += '</ul>';
            
            // Page info
            var start = (self.currentPage - 1) * self.recordsPerPage + 1;
            var end = Math.min(self.currentPage * self.recordsPerPage, self.filteredRecords.length);
            html += '<div class="page-info">Showing ' + start + ' to ' + end + ' of ' + self.filteredRecords.length + ' records</div>';
            
            html += '</div>';
            
            $('#recordsPagination').html(html);
        },
        
        /**
         * Go to specific page
         */
        goToPage: function(page) {
            var totalPages = Math.ceil(this.filteredRecords.length / this.recordsPerPage);
            if (page < 1 || page > totalPages) {
                return;
            }
            this.currentPage = page;
            this.displayRecords();
            this.displayPagination();
            
            // Scroll to top of records
            $('html, body').animate({
                scrollTop: $('#recordsContainer').offset().top - 100
            }, 300);
        },
        
        /**
         * Show add record modal
         */
        showAddRecordModal: function() {
            $('#recordModalTitle').text('Add DNS Record');
            $('#recordForm')[0].reset();
            $('#recordId').val('');
            $('#priorityGroup').hide();
            $('#recordModal').modal('show');
        },
        
        /**
         * Handle record type change
         */
        handleTypeChange: function() {
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
                case 'NS':
                    contentHelp = 'Enter a nameserver (e.g., ns1.example.com)';
                    break;
                case 'SRV':
                    contentHelp = 'Enter target (e.g., server.example.com)';
                    break;
                case 'CAA':
                    contentHelp = 'Enter CAA record (e.g., 0 issue "letsencrypt.org")';
                    break;
            }
            
            $('#contentHelp').text(contentHelp);
        },
        
        /**
         * Save record
         */
        saveRecord: function() {
            var self = this;
            var formData = {
                ajax: 1,
                zone_id: self.zoneId,
                record_id: $('#recordId').val(),
                type: $('#recordType').val(),
                name: $('#recordName').val(),
                content: $('#recordContent').val(),
                ttl: $('#recordTTL').val(),
                priority: $('#recordPriority').val()
            };
            
            var action = formData.record_id ? 'update_record' : 'add_record';
            formData.ajax_action = action;
            
            // Disable save button
            var $saveBtn = $('.modal-footer .btn-primary');
            $saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: self.moduleLink,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#recordModal').modal('hide');
                        self.loadRecords();
                        self.showAlert('success', response.message);
                    } else {
                        $('#validationMessages').html('<div class="alert alert-danger">' + self.escapeHtml(response.message) + '</div>');
                        $saveBtn.prop('disabled', false).html('Save Record');
                    }
                },
                error: function() {
                    $('#validationMessages').html('<div class="alert alert-danger">Request failed. Please try again.</div>');
                    $saveBtn.prop('disabled', false).html('Save Record');
                }
            });
        },
        
        /**
         * Edit record
         */
        editRecord: function(recordId) {
            var self = this;
            var record = self.allRecords.find(function(r) { return r.id == recordId; });
            
            if (!record) return;
            
            $('#recordModalTitle').text('Edit DNS Record');
            $('#recordId').val(record.id);
            $('#recordType').val(record.type);
            $('#recordName').val(record.name);
            $('#recordContent').val(record.content);
            $('#recordTTL').val(record.ttl);
            
            if (record.priority) {
                $('#recordPriority').val(record.priority);
                $('#priorityGroup').show();
            } else {
                $('#priorityGroup').hide();
            }
            
            this.handleTypeChange();
            $('#recordModal').modal('show');
        },
        
        /**
         * Delete record
         */
        deleteRecord: function(recordId) {
            var self = this;
            
            if (!confirm('Are you sure you want to delete this record?')) {
                return;
            }
            
            $.ajax({
                url: self.moduleLink,
                type: 'POST',
                data: {
                    ajax: 1,
                    ajax_action: 'delete_record',
                    record_id: recordId
                },
                success: function(response) {
                    if (response.success) {
                        self.loadRecords();
                        self.showAlert('success', response.message);
                    } else {
                        self.showAlert('danger', response.message);
                    }
                }
            });
        },
        
        /**
         * Bulk delete records
         */
        bulkDeleteRecords: function() {
            var self = this;
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
                url: self.moduleLink,
                type: 'POST',
                data: {
                    ajax: 1,
                    ajax_action: 'bulk_delete',
                    record_ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        self.loadRecords();
                        self.showAlert('success', response.message);
                    } else {
                        self.showAlert('danger', response.message);
                    }
                }
            });
        },
        
        /**
         * Toggle select all
         */
        toggleSelectAll: function() {
            var checked = $('#selectAll').prop('checked');
            $('.record-checkbox').prop('checked', checked);
            this.updateBulkDeleteButton();
        },
        
        /**
         * Update bulk delete button
         */
        updateBulkDeleteButton: function() {
            var anyChecked = $('.record-checkbox:checked').length > 0;
            $('#bulkDeleteBtn').toggle(anyChecked);
        },
        
        /**
         * Apply template
         */
        applyTemplate: function(templateId) {
            var self = this;
            
            if (!confirm('This will add DNS records from the template. Continue?')) {
                return;
            }
            
            $.ajax({
                url: self.moduleLink,
                type: 'POST',
                data: {
                    ajax: 1,
                    ajax_action: 'apply_template',
                    zone_id: self.zoneId,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        self.loadRecords();
                        self.showAlert('success', response.message);
                        // Switch to records tab
                        $('#dnsManagementTabs a[href="#records"]').tab('show');
                    } else {
                        self.showAlert('danger', response.message);
                    }
                }
            });
        },
        
        /**
         * Load DNSSEC info
         */
        loadDNSSEC: function() {
            var self = this;
            
            $.ajax({
                url: self.moduleLink,
                type: 'GET',
                data: {
                    ajax: 1,
                    ajax_action: 'get_dnssec',
                    zone_id: self.zoneId
                },
                success: function(response) {
                    if (response.success) {
                        self.displayDNSSEC(response.data);
                    } else {
                        $('#dnssecContainer').html('<div class="alert alert-danger">' + self.escapeHtml(response.message) + '</div>');
                    }
                }
            });
        },
        
        /**
         * Display DNSSEC info
         */
        displayDNSSEC: function(data) {
            var self = this;
            var html = '<div class="dnssec-status ' + (data && data.enabled ? 'enabled' : 'disabled') + '">';
            html += '<div class="form-horizontal">';
            html += '<div class="form-group">';
            html += '<label class="col-sm-3 control-label">DNSSEC Status:</label>';
            html += '<div class="col-sm-9">';
            
            if (data && data.enabled) {
                html += '<span class="label label-success">Enabled</span>';
                html += '<button class="btn btn-danger btn-sm" style="margin-left: 10px;" onclick="whiteDNSZone.toggleDNSSEC(false)">Disable DNSSEC</button>';
                
                if (data.ds_records) {
                    html += '<hr><h5>DS Records</h5>';
                    html += '<p class="help-block">Add these DS records to your domain registrar:</p>';
                    html += '<pre>' + JSON.stringify(data.ds_records, null, 2) + '</pre>';
                }
            } else {
                html += '<span class="label label-default">Disabled</span>';
                html += '<button class="btn btn-success btn-sm" style="margin-left: 10px;" onclick="whiteDNSZone.toggleDNSSEC(true)">Enable DNSSEC</button>';
            }
            
            html += '</div></div></div></div>';
            $('#dnssecContainer').html(html);
        },
        
        /**
         * Toggle DNSSEC
         */
        toggleDNSSEC: function(enable) {
            var self = this;
            var action = enable ? 'enable' : 'disable';
            
            if (!confirm('Are you sure you want to ' + action + ' DNSSEC?')) {
                return;
            }
            
            $.ajax({
                url: self.moduleLink,
                type: 'POST',
                data: {
                    ajax: 1,
                    ajax_action: 'toggle_dnssec',
                    zone_id: self.zoneId,
                    enable: enable
                },
                success: function(response) {
                    if (response.success) {
                        self.loadDNSSEC();
                        self.showAlert('success', response.message);
                    } else {
                        self.showAlert('danger', response.message);
                    }
                }
            });
        },
        
        /**
         * Load audit log
         */
        loadAuditLog: function() {
            var self = this;
            
            $.ajax({
                url: self.moduleLink,
                type: 'GET',
                data: {
                    ajax: 1,
                    ajax_action: 'get_audit_log',
                    zone_id: self.zoneId
                },
                success: function(response) {
                    if (response.success) {
                        self.displayAuditLog(response.logs);
                    } else {
                        $('#auditContainer').html('<div class="alert alert-danger">' + self.escapeHtml(response.message) + '</div>');
                    }
                }
            });
        },
        
        /**
         * Display audit log
         */
        displayAuditLog: function(logs) {
            var self = this;
            
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
                html += '<td>' + self.escapeHtml(log.details) + '</td>';
                html += '<td>' + log.ip_address + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            $('#auditContainer').html(html);
        },
        
        /**
         * Check propagation
         */
        checkPropagation: function() {
            var self = this;
            var domain = $('#propDomain').val();
            var type = $('#propType').val();
            
            $('#propagationResults').show();
            $('#propagationData').html('<div class="loading-spinner"><i class="fa fa-spinner fa-spin"></i> Checking propagation...</div>');
            
            $.ajax({
                url: self.moduleLink,
                type: 'GET',
                data: {
                    ajax: 1,
                    ajax_action: 'check_propagation',
                    domain: domain,
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        self.displayPropagation(response.data);
                    } else {
                        $('#propagationData').html('<div class="alert alert-danger">' + self.escapeHtml(response.message) + '</div>');
                    }
                }
            });
        },
        
        /**
         * Display propagation results
         */
        displayPropagation: function(data) {
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
        },
        
        /**
         * Show alert
         */
        showAlert: function(type, message) {
            var alert = '<div class="alert alert-' + type + ' alert-dismissible whitednszone-alert">';
            alert += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
            alert += message;
            alert += '</div>';
            
            $('body').append(alert);
            
            setTimeout(function() {
                $('.whitednszone-alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        },
        
        /**
         * HTML escape
         */
        escapeHtml: function(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    };
    
    // Expose to global scope
    window.whiteDNSZone = whiteDNSZone;
    
    // Backwards compatibility aliases
    window.showAddRecordModal = function() { whiteDNSZone.showAddRecordModal(); };
    window.handleTypeChange = function() { whiteDNSZone.handleTypeChange(); };
    window.saveRecord = function() { whiteDNSZone.saveRecord(); };
    window.editRecord = function(id) { whiteDNSZone.editRecord(id); };
    window.deleteRecord = function(id) { whiteDNSZone.deleteRecord(id); };
    window.bulkDeleteRecords = function() { whiteDNSZone.bulkDeleteRecords(); };
    window.applyTemplate = function(id) { whiteDNSZone.applyTemplate(id); };
    window.checkPropagation = function() { whiteDNSZone.checkPropagation(); };
    
})(jQuery);
