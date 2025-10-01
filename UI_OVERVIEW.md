# WhiteDNSZone WHMCS Addon - UI Overview

## Client Area Interface

### 1. Zone List Page

```
┌────────────────────────────────────────────────────────────────────┐
│                      DNS Zone Management                           │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  Manage your DNS zones and records for all your domains.          │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Domain          │ Status  │ Records │ Created    │ Actions   │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │ example.com     │ Active  │   15    │ 2024-01-01 │ [Manage] │ │
│  │ example.org     │ Active  │   12    │ 2024-01-05 │ [Manage] │ │
│  │ example.net     │ Active  │    8    │ 2024-01-10 │ [Manage] │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 2. Zone Management Page - DNS Records Tab

```
┌────────────────────────────────────────────────────────────────────┐
│  example.com - DNS Management                                      │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  [Records] [Templates] [DNSSEC] [Audit Log] [Propagation Check]   │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ DNS Records                          [Add Record] [Delete ✓] │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │                                                              │ │
│  │  ℹ Nameservers: dns1.whitednszone.com, dns2.whitednszone.com│ │
│  │                                                              │ │
│  │  ┌────────────────────────────────────────────────────────┐ │ │
│  │  │ □ │ Type │ Name │ Content      │ TTL  │ Pri │ Actions │ │ │
│  │  ├────────────────────────────────────────────────────────┤ │ │
│  │  │ □ │  A   │  @   │ 192.0.2.1    │ 3600 │  -  │ ✏️ 🗑️  │ │ │
│  │  │ □ │ CNAME│ www  │ example.com  │ 3600 │  -  │ ✏️ 🗑️  │ │ │
│  │  │ □ │  MX  │  @   │ mail.ex.com  │ 3600 │ 10  │ ✏️ 🗑️  │ │ │
│  │  │ □ │ TXT  │  @   │ v=spf1...    │ 3600 │  -  │ ✏️ 🗑️  │ │ │
│  │  └────────────────────────────────────────────────────────┘ │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 3. Add/Edit Record Modal

```
┌──────────────────────────────────────────┐
│  Add DNS Record                    [×]   │
├──────────────────────────────────────────┤
│                                          │
│  Type:         [A ▼]                     │
│                                          │
│  Name:         [@                    ]   │
│                Use @ for root domain     │
│                                          │
│  Content:      [192.0.2.1            ]   │
│                Enter an IPv4 address     │
│                                          │
│  TTL:          [3600 (1 hour) ▼]         │
│                                          │
│  ──────────────────────────────────      │
│                                          │
│           [Cancel] [Save Record]         │
│                                          │
└──────────────────────────────────────────┘
```

### 4. Templates Tab

```
┌────────────────────────────────────────────────────────────────────┐
│  example.com - DNS Management                                      │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  [Records] [Templates] [DNSSEC] [Audit Log] [Propagation Check]   │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ DNS Templates & Presets                                      │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │                                                              │ │
│  │  Apply pre-configured DNS record templates for common        │ │
│  │  services.                                                   │ │
│  │                                                              │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │ │
│  │  │ Google       │  │ Office 365   │  │ Mailgun      │      │ │
│  │  │ Workspace    │  │              │  │              │      │ │
│  │  ├──────────────┤  ├──────────────┤  ├──────────────┤      │ │
│  │  │              │  │              │  │              │      │ │
│  │  │ DNS records  │  │ DNS records  │  │ DNS records  │      │ │
│  │  │ for Google   │  │ for Office   │  │ for Mailgun  │      │ │
│  │  │ Workspace    │  │ 365 email    │  │ email        │      │ │
│  │  │ email        │  │              │  │ service      │      │ │
│  │  │              │  │              │  │              │      │ │
│  │  │[Apply Temp]  │  │[Apply Temp]  │  │[Apply Temp]  │      │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘      │ │
│  │                                                              │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 5. DNSSEC Tab

```
┌────────────────────────────────────────────────────────────────────┐
│  example.com - DNS Management                                      │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  [Records] [Templates] [DNSSEC] [Audit Log] [Propagation Check]   │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ DNSSEC Management                                            │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │                                                              │ │
│  │  DNSSEC Status:  [✓ Enabled]  [Disable DNSSEC]              │ │
│  │                                                              │ │
│  │  ─────────────────────────────────────────────               │ │
│  │                                                              │ │
│  │  DS Records                                                  │ │
│  │  Add these DS records to your domain registrar:             │ │
│  │                                                              │ │
│  │  ┌────────────────────────────────────────────────────────┐ │ │
│  │  │ Key Tag:     12345                                     │ │ │
│  │  │ Algorithm:   8 (RSA/SHA-256)                           │ │ │
│  │  │ Digest Type: 2 (SHA-256)                               │ │ │
│  │  │ Digest:      1234567890abcdef...                       │ │ │
│  │  └────────────────────────────────────────────────────────┘ │ │
│  │                                                              │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 6. Audit Log Tab

```
┌────────────────────────────────────────────────────────────────────┐
│  example.com - DNS Management                                      │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  [Records] [Templates] [DNSSEC] [Audit Log] [Propagation Check]   │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Audit Log                                                    │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │                                                              │ │
│  │  ┌────────────────────────────────────────────────────────┐ │ │
│  │  │ Date       │ Action  │ Details           │ IP Address │ │ │
│  │  ├────────────────────────────────────────────────────────┤ │ │
│  │  │ 2024-01-15 │ CREATE  │ Created A: @      │ 192.0.2.1  │ │ │
│  │  │ 10:30:25   │         │                   │            │ │ │
│  │  │ 2024-01-15 │ UPDATE  │ Updated A: www    │ 192.0.2.1  │ │ │
│  │  │ 11:45:10   │         │                   │            │ │ │
│  │  │ 2024-01-15 │ DELETE  │ Deleted CNAME:old │ 192.0.2.1  │ │ │
│  │  │ 14:20:00   │         │                   │            │ │ │
│  │  └────────────────────────────────────────────────────────┘ │ │
│  │                                                              │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 7. Propagation Check Tab

```
┌────────────────────────────────────────────────────────────────────┐
│  example.com - DNS Management                                      │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  [Records] [Templates] [DNSSEC] [Audit Log] [Propagation Check]   │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ DNS Propagation Check                                        │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │                                                              │ │
│  │  Domain:  [example.com      ]  Type: [A ▼]  [Check]         │ │
│  │                                                              │ │
│  │  ─────────────────────────────────────────────               │ │
│  │                                                              │ │
│  │  ┌────────────────────────────────────────────────────────┐ │ │
│  │  │ Server         │ Location      │ Result    │ Status   │ │ │
│  │  ├────────────────────────────────────────────────────────┤ │ │
│  │  │ Google DNS     │ USA          │ 192.0.2.1 │ ✓ OK     │ │ │
│  │  │ Cloudflare DNS │ Global       │ 192.0.2.1 │ ✓ OK     │ │ │
│  │  │ OpenDNS        │ USA          │ 192.0.2.1 │ ✓ OK     │ │ │
│  │  │ Level3         │ USA          │ 192.0.2.1 │ ✓ OK     │ │ │
│  │  │ Quad9          │ Switzerland  │ 192.0.2.1 │ ✓ OK     │ │ │
│  │  └────────────────────────────────────────────────────────┘ │ │
│  │                                                              │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

## Admin Area Interface

### 1. Admin Dashboard

```
┌────────────────────────────────────────────────────────────────────┐
│  WhiteDNSZone Dashboard                                            │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌──────────┐ │
│  │     150     │  │     1,250   │  │      45     │  │   8.3    │ │
│  │ Total Zones │  │   Records   │  │    Users    │  │ Avg/Zone │ │
│  └─────────────┘  └─────────────┘  └─────────────┘  └──────────┘ │
│                                                                    │
│  [Dashboard] [All Zones] [All Records] [Audit Logs]               │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ Recent Changes                                               │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │ Date       │ User ID │ Action │ Details        │ IP Address │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │ 2024-01-15 │   123   │ CREATE │ A record: www  │ 192.0.2.1  │ │
│  │ 2024-01-15 │   124   │ UPDATE │ MX record: @   │ 192.0.2.2  │ │
│  │ 2024-01-15 │   123   │ DELETE │ CNAME: old     │ 192.0.2.1  │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
│  ┌──────────────────┐  ┌────────────────────────────────────────┐ │
│  │ Top Users        │  │ Record Type Distribution               │ │
│  ├──────────────────┤  ├────────────────────────────────────────┤ │
│  │ User │ Zones     │  │ Type   │ Count                         │ │
│  ├──────────────────┤  ├────────────────────────────────────────┤ │
│  │ 123  │ 15        │  │ A      │ 450                           │ │
│  │ 124  │ 12        │  │ CNAME  │ 320                           │ │
│  │ 125  │ 10        │  │ MX     │ 180                           │ │
│  │ 126  │  8        │  │ TXT    │ 150                           │ │
│  └──────────────────┘  └────────────────────────────────────────┘ │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

### 2. All Zones View (Admin)

```
┌────────────────────────────────────────────────────────────────────┐
│  WhiteDNSZone Management                                           │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  [Dashboard] [All Zones] [All Records] [Audit Logs]               │
│                                                                    │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │ All DNS Zones                                                │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │ ID │Domain       │Client          │Status │Created          │ │
│  ├──────────────────────────────────────────────────────────────┤ │
│  │ 1  │example.com  │John Doe        │Active │2024-01-01 10:00 │ │
│  │    │             │john@email.com  │       │                 │ │
│  │ 2  │example.org  │Jane Smith      │Active │2024-01-05 14:30 │ │
│  │    │             │jane@email.com  │       │                 │ │
│  │ 3  │example.net  │Bob Johnson     │Active │2024-01-10 09:15 │ │
│  │    │             │bob@email.com   │       │                 │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                    │
│  « Previous  [1] [2] [3] [4] [5]  Next »                          │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

## Color Scheme

- **Primary Color**: #337ab7 (Bootstrap Blue)
- **Success**: #5cb85c (Green)
- **Danger**: #d9534f (Red)
- **Warning**: #f0ad4e (Orange)
- **Info**: #5bc0de (Light Blue)
- **Background**: #ffffff (White)
- **Border**: #dddddd (Light Gray)
- **Text**: #333333 (Dark Gray)

## Icons Used

- 🌐 Globe - DNS zones
- ✏️ Pencil - Edit
- 🗑️ Trash - Delete
- ✓ Checkmark - Success/Active
- × Cross - Close/Cancel
- ℹ️ Info - Information
- 🔄 Refresh - Reload/Check
- 🛡️ Shield - DNSSEC
- 📋 Clipboard - Audit log
- ⚙️ Gear - Settings

## Responsive Breakpoints

- **Desktop**: > 992px (full layout)
- **Tablet**: 768px - 991px (adjusted layout)
- **Mobile**: < 768px (stacked layout)

## Accessibility Features

- ARIA labels on all interactive elements
- Keyboard navigation support
- Color contrast compliance (WCAG 2.1 AA)
- Screen reader friendly
- Focus indicators
- Semantic HTML structure

## User Experience Features

- **Loading States**: Spinners during API calls
- **Empty States**: Helpful messages when no data
- **Error States**: Clear error messages with solutions
- **Success Messages**: Confirmation of actions
- **Tooltips**: Help text on hover
- **Validation**: Real-time form validation
- **Auto-save**: Drafts for long forms (future)
- **Undo**: Ability to revert changes (future)
