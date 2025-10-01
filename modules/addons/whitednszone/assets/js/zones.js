/**
 * WhiteDNSZone WHMCS Module - Zones List JavaScript
 * Client-side search and filter for zones list
 */

(function($) {
    'use strict';
    
    var zonesList = {
        allZones: [],
        currentPage: 1,
        zonesPerPage: 10,
        searchTerm: '',
        filterStatus: '',
        
        /**
         * Initialize zones list
         */
        init: function() {
            this.cacheZones();
            this.setupSearchFilter();
            this.displayZones();
        },
        
        /**
         * Cache all zones from the table
         */
        cacheZones: function() {
            var self = this;
            self.allZones = [];
            
            $('#zonesTable tbody tr').each(function() {
                var $row = $(this);
                self.allZones.push({
                    element: $row.clone(),
                    domain: $row.find('td:eq(0)').text().trim().toLowerCase(),
                    status: $row.find('td:eq(1)').text().trim().toLowerCase(),
                    records: $row.find('td:eq(2)').text().trim(),
                    created: $row.find('td:eq(3)').text().trim()
                });
            });
        },
        
        /**
         * Setup search and filter
         */
        setupSearchFilter: function() {
            var self = this;
            
            // Search input
            $('#zoneSearch').on('keyup', function() {
                self.searchTerm = $(this).val().toLowerCase();
                self.currentPage = 1;
                self.filterAndDisplay();
            });
            
            // Status filter
            $('#zoneStatusFilter').on('change', function() {
                self.filterStatus = $(this).val().toLowerCase();
                self.currentPage = 1;
                self.filterAndDisplay();
            });
            
            // Zones per page
            $('#zonesPerPage').on('change', function() {
                self.zonesPerPage = parseInt($(this).val());
                self.currentPage = 1;
                self.filterAndDisplay();
            });
            
            // Clear filters
            $('#clearFilters').on('click', function(e) {
                e.preventDefault();
                $('#zoneSearch').val('');
                $('#zoneStatusFilter').val('');
                $('#zonesPerPage').val('10');
                self.searchTerm = '';
                self.filterStatus = '';
                self.zonesPerPage = 10;
                self.currentPage = 1;
                self.filterAndDisplay();
            });
        },
        
        /**
         * Filter and display zones
         */
        filterAndDisplay: function() {
            var self = this;
            
            var filtered = self.allZones.filter(function(zone) {
                var matchesSearch = true;
                var matchesStatus = true;
                
                if (self.searchTerm) {
                    matchesSearch = zone.domain.indexOf(self.searchTerm) !== -1;
                }
                
                if (self.filterStatus) {
                    matchesStatus = zone.status.indexOf(self.filterStatus) !== -1;
                }
                
                return matchesSearch && matchesStatus;
            });
            
            self.displayZones(filtered);
            self.displayPagination(filtered.length);
        },
        
        /**
         * Display zones
         */
        displayZones: function(filtered) {
            var self = this;
            var zones = filtered || self.allZones;
            
            if (zones.length === 0) {
                var message = 'No DNS zones found.';
                if (self.searchTerm || self.filterStatus) {
                    message = 'No zones match your filter criteria.';
                }
                
                $('#zonesTable tbody').html(
                    '<tr><td colspan="5" class="text-center">' +
                    '<div class="empty-state">' +
                    '<i class="fa fa-search"></i>' +
                    '<h4>' + message + '</h4>' +
                    '</div>' +
                    '</td></tr>'
                );
                $('#zonesStats').html('Showing 0 zones');
                return;
            }
            
            // Calculate pagination
            var start = (self.currentPage - 1) * self.zonesPerPage;
            var end = start + self.zonesPerPage;
            var pageZones = zones.slice(start, end);
            
            // Display zones
            var tbody = $('#zonesTable tbody');
            tbody.empty();
            
            pageZones.forEach(function(zone) {
                tbody.append(zone.element);
            });
            
            // Update stats
            var statsStart = start + 1;
            var statsEnd = Math.min(end, zones.length);
            $('#zonesStats').html('Showing ' + statsStart + ' to ' + statsEnd + ' of ' + zones.length + ' zones');
        },
        
        /**
         * Display pagination
         */
        displayPagination: function(totalZones) {
            var self = this;
            var totalPages = Math.ceil(totalZones / self.zonesPerPage);
            
            if (totalPages <= 1) {
                $('#zonesPagination').html('');
                return;
            }
            
            var html = '<ul class="pagination">';
            
            // Previous button
            html += '<li' + (self.currentPage === 1 ? ' class="disabled"' : '') + '>';
            html += '<a href="#" data-page="' + (self.currentPage - 1) + '">&laquo;</a>';
            html += '</li>';
            
            // Page numbers
            var startPage = Math.max(1, self.currentPage - 2);
            var endPage = Math.min(totalPages, self.currentPage + 2);
            
            if (startPage > 1) {
                html += '<li><a href="#" data-page="1">1</a></li>';
                if (startPage > 2) {
                    html += '<li class="disabled"><a href="#">...</a></li>';
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                html += '<li' + (i === self.currentPage ? ' class="active"' : '') + '>';
                html += '<a href="#" data-page="' + i + '">' + i + '</a>';
                html += '</li>';
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += '<li class="disabled"><a href="#">...</a></li>';
                }
                html += '<li><a href="#" data-page="' + totalPages + '">' + totalPages + '</a></li>';
            }
            
            // Next button
            html += '<li' + (self.currentPage === totalPages ? ' class="disabled"' : '') + '>';
            html += '<a href="#" data-page="' + (self.currentPage + 1) + '">&raquo;</a>';
            html += '</li>';
            
            html += '</ul>';
            
            $('#zonesPagination').html(html);
            
            // Bind pagination click events
            $('#zonesPagination a').on('click', function(e) {
                e.preventDefault();
                var $link = $(this);
                if ($link.parent().hasClass('disabled') || $link.parent().hasClass('active')) {
                    return;
                }
                var page = parseInt($link.data('page'));
                self.goToPage(page);
            });
        },
        
        /**
         * Go to specific page
         */
        goToPage: function(page) {
            var totalPages = Math.ceil(this.allZones.length / this.zonesPerPage);
            if (page < 1 || page > totalPages) {
                return;
            }
            this.currentPage = page;
            this.filterAndDisplay();
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('#zonesTable').offset().top - 100
            }, 300);
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        if ($('#zonesTable').length > 0) {
            zonesList.init();
        }
    });
    
    // Expose to global scope
    window.zonesList = zonesList;
    
})(jQuery);
