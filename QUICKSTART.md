# WhiteDNSZone WHMCS Addon - Quick Start Guide

## 5-Minute Setup

### Step 1: Upload Files (1 minute)
```bash
# Upload to your WHMCS installation
cd /path/to/whmcs
# Upload the modules directory from the repository
```

### Step 2: Activate Module (1 minute)
1. Login to WHMCS Admin
2. Go to **Setup → Addon Modules**
3. Find **WhiteDNSZone Manager**
4. Click **Activate**

### Step 3: Configure API (2 minutes)
1. Click **Configure**
2. Enter your **API Key** from https://my.whitednszone.com
3. Keep default values:
   - API URL: `https://my.whitednszone.com/api`
   - NS1: `dns1.whitednszone.com`
   - NS2: `dns2.whitednszone.com`
4. Enable **Audit Logging** ✓
5. Enable **Propagation Check** ✓
6. Click **Save Changes**

### Step 4: Set Access Control (1 minute)
1. Select admin roles that can access the module
2. Click **Save Changes**

### Done! 🎉

## Quick Usage Guide

### For Clients

#### View DNS Zones
1. Login to client area
2. Go to **Addons → WhiteDNSZone Manager**
3. See list of all your DNS zones

#### Manage DNS Records
1. Click **Manage** next to a zone
2. You'll see 5 tabs:
   - **DNS Records** - Add/edit/delete records
   - **Templates** - Apply pre-configured setups
   - **DNSSEC** - Enable DNS security
   - **Audit Log** - View change history
   - **Propagation Check** - Verify DNS changes

#### Add a DNS Record
1. Click **Add Record** button
2. Select record type (A, CNAME, MX, etc.)
3. Enter:
   - Name: `@` for root or subdomain name
   - Content: IP address or target
   - TTL: Select from dropdown (default: 1 hour)
   - Priority: Only for MX/SRV records
4. Click **Save Record**

#### Apply a Template
1. Go to **Templates** tab
2. Choose from:
   - **Google Workspace** - For Gmail
   - **Office 365** - For Microsoft email
   - **Mailgun** - For email service
3. Click **Apply Template**
4. Switch to **DNS Records** tab to see added records

### For Administrators

#### View Dashboard
1. Go to **Addons → WhiteDNSZone Manager**
2. See statistics:
   - Total zones
   - Total records
   - Active users
   - Recent changes

#### View All Zones
1. Click **All Zones** tab
2. See all DNS zones across all clients
3. View client information and zone status

#### View Audit Logs
1. Click **Audit Logs** tab
2. See all DNS changes system-wide
3. Filter by client, date, or action type

## Common Tasks

### Add A Record (Point domain to server)
```
Type: A
Name: @
Content: 192.0.2.1
TTL: 3600
```

### Add WWW Subdomain
```
Type: CNAME
Name: www
Content: example.com
TTL: 3600
```

### Add Mail Server
```
Type: MX
Name: @
Content: mail.example.com
Priority: 10
TTL: 3600
```

### Add SPF Record
```
Type: TXT
Name: @
Content: v=spf1 include:_spf.example.com ~all
TTL: 3600
```

### Setup Google Workspace
1. Go to **Templates** tab
2. Click **Apply Template** on **Google Workspace**
3. Done! 5 MX records added automatically

## Troubleshooting

### "API Connection Failed"
**Solution**: Check your API key in module configuration

### "Zone Not Found"
**Solution**: Ensure the zone is created in WhiteDNSZone first

### "Invalid Record"
**Solution**: Check validation messages - they tell you exactly what's wrong

### Records Not Showing
**Solution**: 
1. Check browser console for errors
2. Verify API connectivity
3. Check WHMCS error logs

## Tips & Best Practices

### TTL Values
- **300 (5 min)**: Use when making changes, for quick updates
- **3600 (1 hour)**: Standard for most records
- **86400 (24 hours)**: Use for stable records that rarely change

### Record Types
- **A**: Use for IPv4 addresses only
- **AAAA**: Use for IPv6 addresses only
- **CNAME**: Never use at root (@), only for subdomains
- **MX**: Always include priority (lower = higher priority)
- **TXT**: Use for verification, SPF, DKIM records

### Security
- Enable audit logging to track all changes
- Review audit logs regularly
- Use strong API keys
- Limit admin access appropriately
- Keep backups of critical zones

### Performance
- Use appropriate TTL values
- Don't create excessive DNS records
- Clean up unused records regularly
- Monitor propagation after changes

## Need More Help?

- **Full Documentation**: See README.md
- **Installation Guide**: See INSTALLATION.md
- **Feature List**: See FEATURES.md
- **API Docs**: https://my.whitednszone.com/swagger
- **WHMCS Docs**: https://docs.whmcs.com

## Next Steps

1. ✓ Module installed and configured
2. ✓ Create test zone
3. ✓ Add test DNS records
4. ✓ Apply a template
5. ✓ Enable DNSSEC
6. ✓ Check propagation
7. ✓ Review audit logs
8. → Train your team
9. → Inform clients
10. → Monitor usage

## Support

For issues with:
- **Module**: Check WHMCS module logs
- **API**: Review WhiteDNSZone API documentation
- **WHMCS**: Consult WHMCS documentation

---

**Quick Reference Card**

| Task | Location | Action |
|------|----------|--------|
| Add Record | Records Tab | Click "Add Record" |
| Delete Record | Records Tab | Click trash icon |
| Apply Template | Templates Tab | Click "Apply Template" |
| Enable DNSSEC | DNSSEC Tab | Click "Enable DNSSEC" |
| View History | Audit Log Tab | View automatically |
| Check Propagation | Propagation Tab | Enter domain, click "Check" |
| Admin Dashboard | Admin Area | Addons → WhiteDNSZone |

---

**Default Nameservers**
```
Primary: dns1.whitednszone.com
Secondary: dns2.whitednszone.com
```

**Common Record Examples**
```
Root domain:        @ A 192.0.2.1
WWW subdomain:      www CNAME example.com
Mail server:        @ MX 10 mail.example.com
Email subdomain:    mail A 192.0.2.2
FTP subdomain:      ftp A 192.0.2.3
SPF record:         @ TXT v=spf1 include:_spf.example.com ~all
```
