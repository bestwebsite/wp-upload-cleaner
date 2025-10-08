# WP Upload Cleaner

**Find and safely remove orphaned files from `wp-content/uploads`.**

When sites are migrated, rebuilt, or heavily edited over time, the uploads folder collects files that are no longer referenced by any media attachment. This plugin scans for those **orphaned files**, lets you preview them in the dashboard, and delete safely — or automate via WP‑CLI.

## Features
- Scan `uploads/` for files not referenced by any attachment
- Preview results in **Tools → Upload Cleaner**
- Delete selected files or **Delete All** from the last scan
- WP‑CLI: `scan`, `delete_all`, `delete <path>`
- Safety checks ensure deletions stay inside the uploads directory

## Installation
1. Upload `wp-upload-cleaner` to `/wp-content/plugins/`
2. Activate via **Plugins**
3. Go to **Tools → Upload Cleaner**

## WP‑CLI
```bash
wp upload-cleaner scan --format=table
wp upload-cleaner delete_all --yes
wp upload-cleaner delete 2025/01/old-image.jpg
```

## Author
Built and maintained by **Best Website** — https://bestwebsite.com  
Contact: support@bestwebsite.com

## License
GPL‑2.0 or later
