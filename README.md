# WhiteDNSZone WHMCS Addon Module

A comprehensive DNS management addon module for WHMCS that integrates with the WhiteDNSZone API (https://my.whitednszone.com/swagger).

## Features

### Client Area
- **Tab-Based Responsive UI** with the following sections:
  - **DNS Records**: Manage all DNS records (A, AAAA, CNAME, MX, TXT, NS, SRV, CAA, PTR)
  - **Templates**: Pre-configured DNS record templates for common services
  - **DNSSEC**: Enable/disable DNSSEC and view DS records
  - **Audit Log**: Track all DNS changes with timestamps and IP addresses
  - **Propagation Check**: Verify DNS propagation across multiple servers

- **DNS Record Management**:
  - Add, edit, and delete DNS records
  - Real-time record validation with suggestions
  - Bulk operations for multiple records
  - Support for all common DNS record types
  - Configurable TTL values

- **Pre-configured Templates**:
  - Google Workspace email configuration
  - Microsoft Office 365 email configuration
  - Mailgun email service configuration
  - Easy one-click template application

### Admin Area
- **Dashboard**: Summary view with statistics
  - Total zones, records, and active users
  - Recent changes and activity log
  - Top users by zone count
  - Record type distribution

- **All Zones**: View all DNS zones across all clients
- **All Records**: View all DNS records across all zones
- **Audit Logs**: Complete audit trail of all DNS changes

### Default Configuration
- **Default Nameservers**: dns1.whitednszone.com, dns2.whitednszone.com
- **Audit Logging**: Optional comprehensive audit logging
- **Propagation Checking**: Optional DNS propagation verification

## Installation

1. **Upload Module Files**:
   ```bash
   # Upload the entire 'modules' directory to your WHMCS installation
   # The structure should be:
   # /path/to/whmcs/modules/addons/whitednszone/
   ```

2. **Activate Module**:
   - Log in to WHMCS Admin Area
   - Navigate to **Setup** → **Addon Modules**
   - Find "WhiteDNSZone Manager" in the list
   - Click **Activate**

3. **Configure Module**:
   - Click **Configure** next to WhiteDNSZone Manager
   - Set the following configuration options:
     - **API URL**: `https://my.whitednszone.com/api` (default)
     - **API Key**: Your WhiteDNSZone API key
     - **Default NS1**: `dns1.whitednszone.com` (default)
     - **Default NS2**: `dns2.whitednszone.com` (default)
     - **Enable Audit Logging**: Yes/No (recommended: Yes)
     - **Enable Propagation Check**: Yes/No (recommended: Yes)
   
4. **Set Access Control**:
   - Configure which admin roles can access the module
   - Save configuration

## Database Tables

The module automatically creates the following database tables during activation:

- `mod_whitednszone_zones`: Stores DNS zone information
- `mod_whitednszone_records`: Stores DNS record details
- `mod_whitednszone_audit`: Stores audit log entries
- `mod_whitednszone_templates`: Stores DNS record templates

## Usage

### Client Area

Clients can access the DNS management interface by:
1. Logging into the client area
2. Navigating to **Addons** → **WhiteDNSZone Manager**
3. Selecting a zone to manage
4. Using the tabbed interface to:
   - View and manage DNS records
   - Apply pre-configured templates
   - Enable/disable DNSSEC
   - View audit logs
   - Check DNS propagation

### Admin Area

Administrators can access the module by:
1. Logging into the admin area
2. Navigating to **Addons** → **WhiteDNSZone Manager**
3. Viewing the dashboard with statistics
4. Managing all zones, records, and audit logs

## API Integration

The module integrates with the WhiteDNSZone API endpoints:

- `GET /zones` - List all zones
- `POST /zones` - Create new zone
- `GET /zones/{id}` - Get zone details
- `DELETE /zones/{id}` - Delete zone
- `GET /zones/{id}/records` - List records
- `POST /zones/{id}/records` - Create record
- `PUT /zones/{id}/records/{id}` - Update record
- `DELETE /zones/{id}/records/{id}` - Delete record
- `POST /zones/{id}/records/bulk` - Bulk create records
- `DELETE /zones/{id}/records/bulk` - Bulk delete records
- `GET /zones/{id}/dnssec` - Get DNSSEC info
- `POST /zones/{id}/dnssec` - Enable DNSSEC
- `DELETE /zones/{id}/dnssec` - Disable DNSSEC
- `GET /zones/{id}/audit` - Get audit log
- `GET /propagation/check` - Check DNS propagation

## DNS Record Validation

The module includes comprehensive validation for DNS records:

- **A Records**: Validates IPv4 addresses
- **AAAA Records**: Validates IPv6 addresses
- **CNAME Records**: Prevents creation at root domain (@)
- **MX Records**: Requires priority value
- **TXT Records**: Warns about records exceeding 255 characters
- **All Records**: Validates record format and content

## Templates

Pre-configured templates include:

### Google Workspace
- MX records for Google mail servers (aspmx.l.google.com, etc.)
- Appropriate priorities (1, 5, 10)
- Standard TTL of 3600 seconds

### Office 365
- MX record for Microsoft mail protection
- SPF TXT record for Office 365
- Autodiscover CNAME record

### Mailgun
- SPF TXT record for Mailgun
- DKIM TXT record (requires custom key)
- Email tracking CNAME record

## Security Features

- **API Authentication**: Secure API key-based authentication
- **Audit Logging**: Complete audit trail with IP addresses
- **Access Control**: WHMCS role-based access control
- **Input Validation**: Comprehensive input validation and sanitization
- **CSRF Protection**: Built-in WHMCS CSRF protection

## Requirements

- WHMCS 7.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher
- cURL extension enabled
- Valid WhiteDNSZone API credentials

## Support

For support with the WhiteDNSZone API, please refer to:
- API Documentation: https://my.whitednszone.com/swagger
- API Support: Contact WhiteDNSZone support

## License

Copyright (c) WhiteDNSZone. All rights reserved.

## Changelog

### Version 1.0.0
- Initial release
- Client area with tab-based UI
- DNS record management (add, edit, delete, bulk operations)
- Pre-configured templates (Google Workspace, Office 365, Mailgun)
- DNSSEC management
- Audit logging
- DNS propagation checking
- Admin dashboard with statistics
- Complete API integration