# WhiteDNSZone WHMCS Addon - Deployment Checklist

## Pre-Deployment

### Requirements Verification
- [ ] WHMCS version 7.0 or higher installed
- [ ] PHP version 7.2 or higher
- [ ] MySQL version 5.6 or higher
- [ ] cURL PHP extension enabled
- [ ] Valid WhiteDNSZone API key obtained
- [ ] Backup of WHMCS database created
- [ ] Backup of WHMCS files created

### Documentation Review
- [ ] Read README.md
- [ ] Review INSTALLATION.md
- [ ] Review FEATURES.md
- [ ] Review QUICKSTART.md
- [ ] Check CHANGELOG.md for version notes

## Deployment Steps

### 1. File Upload (Check each step)
- [ ] Create directory: `/path/to/whmcs/modules/addons/whitednszone/`
- [ ] Upload `whitednszone.php` to addon directory
- [ ] Upload `version.php` to addon directory
- [ ] Upload `config.sample.php` to addon directory
- [ ] Create and upload `lib/` directory
- [ ] Upload `lib/ApiClient.php`
- [ ] Upload `lib/Client.php`
- [ ] Upload `lib/Admin.php`
- [ ] Create and upload `templates/` directory
- [ ] Upload `templates/zones.tpl`
- [ ] Upload `templates/manage.tpl`
- [ ] Upload `templates/error.tpl`
- [ ] Create and upload `templates/admin/` directory
- [ ] Upload `templates/admin/dashboard.php`
- [ ] Upload `templates/admin/zones.php`
- [ ] Upload `templates/admin/records.php`
- [ ] Upload `templates/admin/logs.php`
- [ ] Create and upload `hooks/` directory
- [ ] Upload `hooks/hooks.php`

### 2. File Permissions
```bash
# Set correct permissions
chmod 755 /path/to/whmcs/modules/addons/whitednszone
chmod 644 /path/to/whmcs/modules/addons/whitednszone/*.php
chmod 755 /path/to/whmcs/modules/addons/whitednszone/lib
chmod 644 /path/to/whmcs/modules/addons/whitednszone/lib/*.php
chmod 755 /path/to/whmcs/modules/addons/whitednszone/templates
chmod 644 /path/to/whmcs/modules/addons/whitednszone/templates/*.tpl
chmod 755 /path/to/whmcs/modules/addons/whitednszone/templates/admin
chmod 644 /path/to/whmcs/modules/addons/whitednszone/templates/admin/*.php
chmod 755 /path/to/whmcs/modules/addons/whitednszone/hooks
chmod 644 /path/to/whmcs/modules/addons/whitednszone/hooks/*.php
```

- [ ] All file permissions set correctly
- [ ] Web server can read all files
- [ ] No files are world-writable

### 3. Module Activation
- [ ] Log in to WHMCS Admin Area
- [ ] Navigate to Setup → Addon Modules
- [ ] Locate "WhiteDNSZone Manager" in the list
- [ ] Click "Activate" button
- [ ] Confirm activation was successful
- [ ] Check for any error messages

### 4. Database Verification
```sql
-- Run these queries to verify tables were created
SHOW TABLES LIKE 'mod_whitednszone%';
-- Should return 4 tables:
-- mod_whitednszone_zones
-- mod_whitednszone_records
-- mod_whitednszone_audit
-- mod_whitednszone_templates

-- Verify template presets were created
SELECT name FROM mod_whitednszone_templates WHERE is_preset = 1;
-- Should return:
-- Google Workspace
-- Office 365
-- Mailgun
```

- [ ] All 4 database tables created
- [ ] 3 template presets exist
- [ ] No database errors in WHMCS logs

### 5. Module Configuration
- [ ] Click "Configure" next to WhiteDNSZone Manager
- [ ] Enter API URL: `https://my.whitednszone.com/api`
- [ ] Enter API Key from WhiteDNSZone account
- [ ] Verify Default NS1: `dns1.whitednszone.com`
- [ ] Verify Default NS2: `dns2.whitednszone.com`
- [ ] Enable "Enable Audit Logging"
- [ ] Enable "Enable Propagation Check"
- [ ] Click "Save Changes"
- [ ] Verify configuration saved successfully

### 6. Access Control
- [ ] Select admin roles that should have access
- [ ] Recommended: Full Administrator
- [ ] Click "Save Changes"
- [ ] Test access with permitted role
- [ ] Test access denial with non-permitted role

### 7. API Connectivity Test
- [ ] Go to Addons → WhiteDNSZone Manager (Admin)
- [ ] Dashboard should load without errors
- [ ] Check for API connection errors
- [ ] Verify statistics display (even if zero)
- [ ] Check WHMCS Module Log for errors

## Post-Deployment Testing

### Client Area Testing
- [ ] Log in as test client
- [ ] Navigate to Addons → WhiteDNSZone Manager
- [ ] Verify zone list displays (or shows empty message)
- [ ] Click "Manage" on a test zone
- [ ] Verify all 5 tabs are visible and clickable
- [ ] Test DNS Records tab loads
- [ ] Test Templates tab displays presets
- [ ] Test DNSSEC tab loads
- [ ] Test Audit Log tab loads
- [ ] Test Propagation Check tab (if enabled)

### Record Management Testing
- [ ] Click "Add Record" button
- [ ] Modal opens correctly
- [ ] All record types available in dropdown
- [ ] Add test A record
- [ ] Verify record appears in list
- [ ] Edit the test record
- [ ] Verify changes saved
- [ ] Delete the test record
- [ ] Verify record removed from list

### Template Testing
- [ ] Go to Templates tab
- [ ] Click "Apply Template" on Google Workspace
- [ ] Confirm application
- [ ] Switch to Records tab
- [ ] Verify 5 MX records were created
- [ ] Verify correct priorities (1, 5, 5, 10, 10)

### Validation Testing
- [ ] Try to create A record with invalid IP
- [ ] Verify error message appears
- [ ] Try to create CNAME at root (@)
- [ ] Verify warning/error appears
- [ ] Try to create MX without priority
- [ ] Verify error message appears

### Admin Area Testing
- [ ] Navigate to Addons → WhiteDNSZone Manager (Admin)
- [ ] Verify dashboard statistics display
- [ ] Click "All Zones" tab
- [ ] Verify zones list with client information
- [ ] Click "All Records" tab
- [ ] Verify records list with domain information
- [ ] Click "Audit Logs" tab
- [ ] Verify audit entries display

### Audit Log Testing
- [ ] Create a test DNS record
- [ ] Go to Audit Log tab
- [ ] Verify action was logged
- [ ] Check timestamp is correct
- [ ] Verify IP address is logged
- [ ] Verify action details are clear

### Propagation Check Testing (if enabled)
- [ ] Go to Propagation Check tab
- [ ] Enter a domain
- [ ] Select record type (A)
- [ ] Click "Check" button
- [ ] Verify results display from multiple servers
- [ ] Check for success/failure indicators

## Security Verification

### Access Control
- [ ] Verify non-logged-in users cannot access
- [ ] Verify clients can only see their own zones
- [ ] Verify admin access works correctly
- [ ] Verify API key is not exposed in HTML
- [ ] Verify no SQL injection vulnerabilities

### Data Validation
- [ ] Test XSS prevention in record content
- [ ] Test SQL injection in form fields
- [ ] Verify CSRF tokens on forms
- [ ] Test file upload restrictions (if any)

## Performance Testing

### Load Testing
- [ ] Create 10 test DNS records
- [ ] Verify page loads in < 2 seconds
- [ ] Create 50 test DNS records
- [ ] Verify page still responsive
- [ ] Test with 100+ records
- [ ] Verify pagination works correctly

### API Testing
- [ ] Monitor API response times
- [ ] Verify no timeout errors
- [ ] Check for rate limiting issues
- [ ] Verify error handling works

## Documentation

### Client Documentation
- [ ] Create client-facing documentation
- [ ] Include screenshots of interface
- [ ] Document common tasks
- [ ] Provide troubleshooting tips
- [ ] Share QUICKSTART.md with clients

### Staff Training
- [ ] Train support staff on module features
- [ ] Document common support issues
- [ ] Create internal FAQ
- [ ] Schedule training session

## Monitoring Setup

### Log Monitoring
- [ ] Set up monitoring for WHMCS Module Log
- [ ] Create alerts for API errors
- [ ] Monitor database table sizes
- [ ] Check for performance issues

### Usage Tracking
- [ ] Track number of zones created
- [ ] Track number of records managed
- [ ] Monitor API usage
- [ ] Track user adoption

## Backup & Recovery

### Backup Verification
- [ ] Verify database backup includes new tables
- [ ] Test database restore procedure
- [ ] Backup module files separately
- [ ] Document recovery procedure

### Disaster Recovery
- [ ] Document rollback procedure
- [ ] Test module deactivation
- [ ] Test module reactivation
- [ ] Verify data integrity after restore

## Communication

### Internal Communication
- [ ] Notify support team of new module
- [ ] Update internal documentation
- [ ] Schedule demo for team
- [ ] Create support ticket templates

### Client Communication
- [ ] Draft announcement email
- [ ] Create knowledge base articles
- [ ] Update client documentation
- [ ] Schedule client webinar (optional)

## Go-Live Checklist

### Final Verification
- [ ] All tests passed
- [ ] No critical errors in logs
- [ ] Performance is acceptable
- [ ] Documentation is complete
- [ ] Team is trained
- [ ] Clients are informed

### Deployment Approval
- [ ] Technical review completed
- [ ] Security review completed
- [ ] Management approval obtained
- [ ] Go-live date confirmed

### Post Go-Live
- [ ] Monitor for 24 hours after launch
- [ ] Check error logs hourly
- [ ] Respond to support tickets promptly
- [ ] Gather user feedback
- [ ] Document lessons learned

## Rollback Plan

### If Issues Occur
1. [ ] Identify the issue
2. [ ] Check if critical or minor
3. [ ] If critical:
   - [ ] Deactivate module immediately
   - [ ] Notify users
   - [ ] Restore from backup if needed
   - [ ] Investigate issue
4. [ ] If minor:
   - [ ] Document issue
   - [ ] Create fix
   - [ ] Test fix
   - [ ] Deploy fix

### Rollback Steps
```sql
-- If needed, remove module data
-- CAUTION: This will delete all DNS zone data!
-- DROP TABLE IF EXISTS mod_whitednszone_zones;
-- DROP TABLE IF EXISTS mod_whitednszone_records;
-- DROP TABLE IF EXISTS mod_whitednszone_audit;
-- DROP TABLE IF EXISTS mod_whitednszone_templates;
```

- [ ] Deactivate module in WHMCS admin
- [ ] Remove module files (optional)
- [ ] Remove database tables (optional, destructive)
- [ ] Notify stakeholders
- [ ] Document reason for rollback

## Success Criteria

### Technical Success
- [ ] Module installs without errors
- [ ] All features work as expected
- [ ] No performance degradation
- [ ] No security vulnerabilities
- [ ] API integration works correctly

### Business Success
- [ ] Clients can manage DNS easily
- [ ] Support tickets decrease
- [ ] Client satisfaction improves
- [ ] Revenue opportunities identified

## Version Information

- **Module Version**: 1.0.0
- **Deployment Date**: ___________
- **Deployed By**: ___________
- **WHMCS Version**: ___________
- **PHP Version**: ___________
- **MySQL Version**: ___________

## Sign-Off

- [ ] Technical Lead: _____________ Date: _____
- [ ] Security Officer: _____________ Date: _____
- [ ] Project Manager: _____________ Date: _____
- [ ] Client Representative: _____________ Date: _____

## Notes

Use this section to document any deployment-specific notes, issues encountered, or customizations made:

```
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

---

**Emergency Contacts**

- WhiteDNSZone Support: ___________
- WHMCS Support: https://www.whmcs.com/support/
- Internal Tech Lead: ___________
- Module Developer: ___________

**Useful Links**

- WhiteDNSZone API Docs: https://my.whitednszone.com/swagger
- WHMCS Module Docs: https://docs.whmcs.com
- GitHub Repository: ___________
