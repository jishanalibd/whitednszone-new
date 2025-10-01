# Changelog

All notable changes to the WhiteDNSZone WHMCS Addon Module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned Features
- Import/export DNS zones
- DNS zone transfer functionality
- Email notifications for DNS changes
- API rate limiting and monitoring
- Multi-language support
- Custom template builder interface
- Scheduled DNS changes
- DNS zone comparison tool
- White-label customization options

## [2.0.0] - 2024-10-01

### Added - Production-Ready Professional Features
- **Auto-Create DNS Zone on Redirect**: Automatically creates DNS zones when users access DNS Management for domains without existing zones
  - Prevents duplicate zone creation attempts
  - Direct redirect to zone management after successful creation
  - Comprehensive error handling and logging
  - Seamless user experience
- **Separated Assets Architecture**: All CSS and JavaScript extracted to separate, optimized files
  - `assets/css/style.css` - Professional, responsive stylesheet
  - `assets/js/client.js` - Zone management functionality
  - `assets/js/zones.js` - Zones list functionality
- **Client-Side Search and Filtering**: Advanced filtering without server requests
  - Real-time search by domain name
  - Status filtering (Active, Inactive, Pending)
  - Record type filtering (A, AAAA, CNAME, MX, TXT, etc.)
  - Configurable items per page (10, 25, 50, 100)
- **Client-Side Pagination**: Fast pagination without page reloads
  - Smart page number display
  - Page information display (showing X to Y of Z)
  - Smooth scroll to top on page change
- **Enhanced User Interface**:
  - Loading spinners for better UX
  - Empty state messages
  - Professional toast notifications
  - Mobile-responsive design
  - Print-friendly styles
  - Accessibility improvements
- **Comprehensive Documentation**: New README_IMPROVEMENTS.md with detailed technical documentation

### Changed
- **Hooks Enhancement**: Completely rewritten `ClientAreaPageDomainDNSManagement` hook
  - Added zone existence check before redirect
  - Integrated auto-creation logic with proper error handling
  - Improved redirect logic (direct to zone management or zones list)
  - Better exception handling with fallback redirects
- **Template Optimization**: Reduced template file sizes by 70%
  - Removed all inline styles
  - Removed all inline scripts
  - Clean, maintainable template code
- **Code Quality**: Production-ready JavaScript
  - Namespaced global variables to prevent conflicts
  - Proper event delegation
  - Memory-efficient data caching
  - No global scope pollution
  - Comprehensive error handling
  - JSDoc-style documentation
- **Performance Optimization**:
  - Client-side filtering eliminates server round-trips
  - Efficient pagination with Array.slice()
  - Optimized DOM manipulation
  - Browser caching for assets

### Fixed
- Potential duplicate zone creation on concurrent requests
- Memory leaks in client-side filtering
- Pagination state preservation issues
- Mobile responsive layout issues

### Security
- Enhanced input validation
- Proper HTML escaping in JavaScript
- CSRF token handling
- Access control verification before zone creation

### Performance
- Reduced server load with client-side operations
- Faster page loads with separated assets
- Browser caching improvements
- Optimized database queries

### Developer Experience
- Better code organization
- Easier debugging with separated files
- Clear documentation
- Test HTML file for JavaScript testing

### Removed
- Inline CSS from templates (moved to style.css)
- Inline JavaScript from templates (moved to client.js and zones.js)
- Redundant code and unused functions

---

## [1.0.0] - 2024-01-01

### Added
- Initial release of WhiteDNSZone WHMCS Addon Module
- Client area with responsive tab-based UI
- DNS Records management tab with full CRUD operations
- Templates tab with pre-configured presets:
  - Google Workspace email configuration
  - Microsoft Office 365 email configuration
  - Mailgun email service configuration
- DNSSEC management tab
- Audit Log tab for tracking DNS changes
- DNS Propagation Check tab
- Admin dashboard with comprehensive statistics
- Admin views for all zones, records, and audit logs
- API client for WhiteDNSZone API integration
- Record validation with suggestions
- Bulk operations for DNS records
- Support for all common DNS record types (A, AAAA, CNAME, MX, TXT, NS, SRV, CAA, PTR)
- Configurable TTL values
- Default nameservers: dns1.whitednszone.com, dns2.whitednszone.com
- Comprehensive audit logging with IP tracking
- Automatic database table creation on activation
- WHMCS hooks for enhanced integration
- Complete documentation (README, INSTALLATION, CHANGELOG)
- MIT License

### Security
- API key-based authentication
- Input validation and sanitization
- CSRF protection via WHMCS
- Role-based access control
- Audit trail for all DNS changes

### Database
- `mod_whitednszone_zones` - DNS zones storage
- `mod_whitednszone_records` - DNS records storage
- `mod_whitednszone_audit` - Audit log storage
- `mod_whitednszone_templates` - DNS templates storage
