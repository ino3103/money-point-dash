# Domain Configuration - Standalone PHP

This is a standalone PHP folder that can be deployed independently on your main domain (e.g., `moneypoint.com`).

## File Structure

```
domain-config/
├── index.php          # Landing page (root)
├── domains.json       # Domain configuration (edit this)
├── api/
│   └── index.php      # API endpoint
├── redirect/
│   └── index.php      # Redirect handler
├── .htaccess         # URL rewriting rules
└── README.md         # This file
```

## Deployment

1. Upload the entire `domain-config` folder to your main domain
2. Access via:
   - Landing page: `https://moneypoint.com/domain-config/` or `https://moneypoint.com/domain-config`
   - API: `https://moneypoint.com/domain-config/api`
   - Redirect: `https://moneypoint.com/domain-config/redirect?domain=ino`

## Managing Domains

Edit `domains.json` to manage domains:

```json
{
  "main_domain": "moneypoint.com",
  "domains": [
    {
      "domain": "ino",
      "name": "INO Company",
      "description": "INO Money Point System",
      "is_active": true
    }
  ]
}
```

### To Add a Domain:
Add a new entry to the `domains` array in `domains.json`.

### To Deactivate:
Set `"is_active": false`

### To Remove:
Delete the entry from the `domains` array.

## API Endpoints

### List All Active Domains
```
GET /api?action=list
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "domain": "ino",
      "name": "INO Company",
      "description": "INO Money Point System",
      "api_url": "https://ino.moneypoint.com/api/v1",
      "web_url": "https://ino.moneypoint.com"
    }
  ]
}
```

### Validate Domain
```
GET /api?action=validate&domain=ino
POST /api?action=validate
```

Response:
```json
{
  "success": true,
  "data": {
    "domain": "ino",
    "name": "INO Company",
    "description": "INO Money Point System",
    "api_url": "https://ino.moneypoint.com/api/v1",
    "web_url": "https://ino.moneypoint.com"
  }
}
```

## Usage Examples

### Mobile App Integration

```javascript
// Validate domain
fetch('https://moneypoint.com/domain-config/api?action=validate&domain=ino')
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const apiUrl = data.data.api_url;
      // Use apiUrl for all API calls
    }
  });

// List all domains
fetch('https://moneypoint.com/domain-config/api?action=list')
  .then(res => res.json())
  .then(data => {
    // Show domain selection to user
    data.data.forEach(domain => {
      console.log(domain.name, domain.api_url);
    });
  });
```

### Web Redirect

```html
<a href="/domain-config/redirect?domain=ino">Go to INO</a>
```

## URL Structure

- **Root** (`/`) → Landing page (`index.php`)
- **API** (`/api`) → API endpoint (`api/index.php`)
- **Redirect** (`/redirect`) → Redirect handler (`redirect/index.php`)

All URLs are clean (no `.php` extensions) thanks to `.htaccess` rewrite rules.

## Notes

- This is standalone PHP - no Laravel dependencies
- Works on any PHP server with mod_rewrite enabled
- No database required
- Simple JSON file-based configuration
- CORS enabled for API endpoints
- `domains.json` is protected from direct access
