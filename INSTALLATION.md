# WhiteDNSZone WHMCS Addon - Installation Guide

## Prerequisites

Before installing the WhiteDNSZone addon module, ensure you have:

1. **WHMCS Installation**: Version 7.0 or higher
2. **PHP Version**: 7.2 or higher
3. **MySQL Version**: 5.6 or higher
4. **PHP Extensions**: cURL must be enabled
5. **API Credentials**: Valid WhiteDNSZone API key from https://my.whitednszone.com

## Step-by-Step Installation

### 1. Upload Module Files

Upload the module files to your WHMCS installation:

```
/path/to/whmcs/
└── modules/
    └── addons/
        └── whitednszone/
            ├── whitednszone.php
            ├── lib/
            │   ├── Admin.php
            │   ├── Client.php
            │   └── ApiClient.php
            ├── templates/
            │   ├── zones.tpl
            │   ├── manage.tpl
            │   ├── error.tpl
            │   └── admin/
            │       ├── dashboard.php
            │       ├── zones.php
            │       ├── records.php
            │       └── logs.php
            ├── hooks/
            │   └── hooks.php
            └── config.sample.php
```

**Using FTP/SFTP:**
- Upload the entire `modules` directory to your WHMCS root
- Ensure proper file permissions (644 for files, 755 for directories)

**Using SSH:**
```bash
cd /path/to/whmcs
# If uploading as zip
unzip whitednszone-module.zip
# Or clone from repository
git clone https://github.com/yourusername/whitednszone-new.git temp
mv temp/modules/addons/whitednszone modules/addons/
rm -rf temp
```

### 2. Set File Permissions

Ensure correct file permissions:

```bash
cd /path/to/whmcs/modules/addons/whitednszone
chmod 644 *.php
chmod 755 lib templates hooks
chmod 644 lib/*.php
chmod 644 templates/*.tpl
chmod 644 templates/admin/*.php
```

### 3. Activate the Module

1. Log in to your **WHMCS Admin Area**
2. Navigate to **Setup → Addon Modules**
3. Locate **WhiteDNSZone Manager** in the module list
4. Click the **Activate** button

### 4. Configure the Module

After activation:

1. Click **Configure** next to WhiteDNSZone Manager
2. Configure the following settings:

   **API Configuration:**
   - **API URL**: `https://my.whitednszone.com/api` (default)
   - **API Key**: Enter your WhiteDNSZone API key

   **Nameserver Configuration:**
   - **Default NS1**: `dns1.whitednszone.com` (default)
   - **Default NS2**: `dns2.whitednszone.com` (default)

   **Feature Settings:**
   - **Enable Audit Logging**: ✓ (Recommended)
   - **Enable Propagation Check**: ✓ (Recommended)

3. Click **Save Changes**

### 5. Set Access Control

Configure which admin roles can access the module:

1. In the module configuration page, scroll to **Access Control**
2. Select the admin roles that should have access
3. Click **Save Changes**

### 6. Verify Installation

1. Check that the database tables were created:
   ```sql
   SHOW TABLES LIKE 'mod_whitednszone%';
   ```
   
   Expected tables:
   - `mod_whitednszone_zones`
   - `mod_whitednszone_records`
   - `mod_whitednszone_audit`
   - `mod_whitednszone_templates`

2. Verify the default templates were created:
   ```sql
   SELECT * FROM mod_whitednszone_templates WHERE is_preset = 1;
   ```

3. Access the admin interface:
   - Navigate to **Addons → WhiteDNSZone Manager**
   - You should see the dashboard with statistics

### 7. Client Area Access

Clients can access the DNS management interface:

1. Client logs into the client area
2. Navigates to **Addons → WhiteDNSZone Manager** or uses the DNS menu item
3. Can manage their DNS zones and records

## Post-Installation Configuration

### Optional: Enable Hooks

If you want additional functionality like custom menu items:

1. Navigate to **System Settings → General Settings → Other**
2. Ensure hooks are enabled (they are by default)

### Optional: Customize Templates

You can customize the appearance by editing the template files:

- Client Area: `modules/addons/whitednszone/templates/`
- Admin Area: `modules/addons/whitednszone/templates/admin/`

### Optional: Add Custom Templates

To add custom DNS record templates:

```sql
INSERT INTO mod_whitednszone_templates 
(name, category, description, records, is_preset, created_at, updated_at) 
VALUES 
('Custom Template', 'custom', 'Description here', 
'[{"type":"A","name":"@","content":"1.2.3.4","ttl":3600}]',
0, NOW(), NOW());
```

## Testing

### Test API Connection

1. In Admin Area, go to **Addons → WhiteDNSZone Manager**
2. The dashboard should load without errors
3. Check for any error messages related to API connectivity

### Test Client Area

1. Log in as a test client
2. Navigate to the WhiteDNSZone interface
3. Try adding a test DNS record
4. Verify record validation works
5. Test applying a template

## Troubleshooting

### Module Doesn't Appear in Addon Modules

**Solution:**
- Verify file permissions
- Check PHP error logs
- Ensure files are in correct directory structure

### API Connection Errors

**Solution:**
- Verify API URL and API key are correct
- Check firewall/network connectivity
- Ensure cURL extension is enabled
- Test API endpoint manually

### Database Errors

**Solution:**
- Check MySQL user has CREATE TABLE permissions
- Verify WHMCS database connection
- Check error logs in WHMCS

### Templates Not Loading

**Solution:**
- Clear WHMCS template cache
- Verify template files exist and have correct permissions
- Check for PHP syntax errors in templates

## Updating

To update the module:

1. **Backup** current installation
2. **Upload** new files (overwrite existing)
3. Clear WHMCS caches:
   ```bash
   rm -rf /path/to/whmcs/templates_c/*
   ```
4. Test functionality

## Uninstallation

To remove the module:

1. Navigate to **Setup → Addon Modules**
2. Click **Deactivate** next to WhiteDNSZone Manager
3. Optionally, remove database tables:
   ```sql
   DROP TABLE IF EXISTS 
   mod_whitednszone_zones,
   mod_whitednszone_records,
   mod_whitednszone_audit,
   mod_whitednszone_templates;
   ```
4. Delete module files:
   ```bash
   rm -rf /path/to/whmcs/modules/addons/whitednszone
   ```

## Support

For installation support:
- Check WHMCS documentation: https://docs.whmcs.com
- Review WhiteDNSZone API docs: https://my.whitednszone.com/swagger
- Check module logs in WHMCS Module Log

## Security Notes

- Keep API keys secure and never commit them to version control
- Regularly update WHMCS and the module
- Enable audit logging to track all DNS changes
- Review access control settings regularly
- Use strong passwords for admin accounts
- Keep backups of DNS zone configurations

## Next Steps

After successful installation:

1. Configure default nameservers for your zones
2. Create DNS zones for your clients
3. Train staff on using the admin interface
4. Inform clients about the DNS management feature
5. Set up monitoring for DNS changes via audit logs
