# Changelog

All notable changes to the WhiteDNSZone WHMCS Addon Module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

## [Unreleased]

### Planned Features
- Import/export DNS zones
- DNS zone transfer functionality
- Advanced DNS record search and filtering
- Email notifications for DNS changes
- API rate limiting and monitoring
- Multi-language support
- Custom template builder interface
- Scheduled DNS changes
- DNS zone comparison tool
- Integration with WHMCS domain management
- White-label customization options
