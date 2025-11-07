# Live Server Deployment Guide - Image Upload System

## Overview
Your code is now configured to **automatically detect** whether it's running on:
- **Local XAMPP** (your computer)
- **Live Server** (your school server)

No code changes needed when deploying! 🎉

---

## How It Works

### Detection Logic
The system checks if the path contains `public_html`:
- **YES** → School server mode → Uses `/uploads` (absolute)
- **NO** → Local mode → Uses `uploads/` (relative)

### Path Examples

**Local (XAMPP):**
- **Stored in DB:** `uploads/u1/p5/image.jpg`
- **File Location:** `/Applications/XAMPP/.../mvc_auth_from_user_html/uploads/u1/p5/image.jpg`
- **HTML Output:** `<img src="http://localhost/.../uploads/u1/p5/image.jpg">`

**Live Server:**
- **Stored in DB:** `/uploads/u1/p5/image.jpg`
- **File Location:** `/home/username/uploads/u1/p5/image.jpg`
- **HTML Output:** `<img src="http://yourserver.com/uploads/u1/p5/image.jpg">`

---

## Deployment Steps

### Option A: Uploads Inside public_html (Simpler)

#### 1. Upload Your Files
```bash
# Using SFTP/FileZilla, upload entire project to:
/home/username/public_html/your-project-folder/
```

#### 2. Create Uploads Directory
```bash
# SSH into your server
ssh username@yourserver.com

# Navigate to your project
cd ~/public_html/your-project-folder/

# Create uploads folder
mkdir uploads
chmod 775 uploads
```

#### 3. Test
Visit: `http://yourserver.com/public_html/your-project-folder/`

Upload an image through your admin panel. It should work automatically!

---

### Option B: Uploads Outside public_html (More Secure - Recommended)

This keeps uploaded files outside the web root for better security.

#### 1. Upload Your PHP Files
```bash
# Upload to public_html
/home/username/public_html/
├── actions/
├── admin/
├── classes/
├── controllers/
├── settings/
├── index.php
└── ... (all your PHP files)
```

#### 2. Create Uploads Directory OUTSIDE public_html
```bash
# SSH into your server
ssh username@yourserver.com

# Create uploads folder at home directory level
cd ~
mkdir uploads
chmod 775 uploads

# Verify structure:
ls -la ~/
# Should show:
# - public_html/
# - uploads/
```

#### 3. Configure Web Server to Serve /uploads

You need to make the `~/uploads` folder accessible via web at `/uploads`.

**For Apache (.htaccess method):**

Create or edit `~/public_html/.htaccess`:
```apache
# Allow access to uploads directory outside public_html
Alias /uploads /home/username/uploads

<Directory /home/username/uploads>
    Options -Indexes
    AllowOverride None
    Require all granted
</Directory>
```

**Or ask your server admin to add to Apache config:**
```apache
Alias /uploads /home/username/uploads

<Directory /home/username/uploads>
    Options -Indexes
    AllowOverride None
    Require all granted
</Directory>
```

#### 4. Set Permissions
```bash
# Make sure PHP can write to uploads
chmod -R 775 ~/uploads

# If needed, set ownership (replace 'username' and 'www-data' with your server's user/group)
chown -R username:www-data ~/uploads
```

#### 5. Test Detection
Create a test file `~/public_html/test_environment.php`:
```php
<?php
require_once 'settings/upload_config.php';

echo "<h1>Environment Detection Test</h1>";
echo "<pre>";
echo "Is School Server: " . (IS_SCHOOL_SERVER ? 'YES' : 'NO') . "\n";
echo "Upload Base Path: " . UPLOADS_BASE_PATH . "\n";
echo "Upload Web Path: " . UPLOADS_WEB_PATH . "\n";
echo "Upload Directory Exists: " . (is_dir(UPLOADS_BASE_PATH) ? 'YES' : 'NO') . "\n";
echo "Upload Directory Writable: " . (is_writable(UPLOADS_BASE_PATH) ? 'YES' : 'NO') . "\n";
echo "</pre>";
?>
```

Visit: `http://yourserver.com/public_html/test_environment.php`

**Expected Output:**
```
Is School Server: YES
Upload Base Path: /home/username/uploads
Upload Web Path: /uploads
Upload Directory Exists: YES
Upload Directory Writable: YES
```

---

## Troubleshooting

### Problem: Images Not Showing

**Check 1: Directory Exists**
```bash
ls -la ~/uploads
# Should show the uploads directory with 775 permissions
```

**Check 2: Test File Access**
```bash
# Create a test image
cd ~/uploads
echo "test" > test.txt

# Try accessing via browser:
# http://yourserver.com/uploads/test.txt
```

If you get **403 Forbidden**, the web server can't access the directory.

**Fix:**
```bash
# Check Apache error log
tail -f /var/log/apache2/error.log
# Or
tail -f ~/logs/error_log

# Fix permissions
chmod 755 ~/uploads
chmod 644 ~/uploads/test.txt
```

### Problem: Upload Fails with Permission Error

**Check PHP Error Log:**
```bash
tail -f ~/public_html/error_log
# Or wherever your PHP error log is
```

**Fix Permissions:**
```bash
# Set directory permissions
find ~/uploads -type d -exec chmod 775 {} \;

# Set file permissions
find ~/uploads -type f -exec chmod 664 {} \;

# Fix ownership (replace with your server's web user)
chown -R username:www-data ~/uploads
```

### Problem: Detection Not Working

The system detects school server by checking if path contains `public_html`.

**If your server uses different structure**, edit `settings/upload_config.php`:

```php
// Change line 10 to match your server's structure
$isSchoolServer = strpos(__DIR__, 'YOUR_SERVER_IDENTIFIER') !== false;
// For example: 'home', 'htdocs', etc.
```

---

## Testing Your Deployment

### 1. Upload Test Image
1. Login as admin
2. Go to admin panel
3. Upload a product image
4. Check database - path should start with `/uploads/` on live server

### 2. View Test
1. Visit shop page
2. Images should load correctly
3. Check browser console for errors
4. Right-click image → "Open in new tab" to see full URL

### 3. Verify Paths

**In Browser Console:**
```javascript
// Check image source
document.querySelectorAll('img').forEach(img => {
    console.log(img.src);
});

// Should show: http://yourserver.com/uploads/u1/p5/image.jpg
```

**In PHP Debug:**
Add to `index.php` or `public/shop.php` temporarily:
```php
<?php
// Enable debug mode
echo "<!-- Image Path Debug:\n";
foreach ($perfumes as $p) {
    echo "DB: " . $p['image'] . "\n";
    echo "Normalized: " . normalize_image_path($p['image']) . "\n\n";
}
echo "-->";
?>
```

---

## Files Modified

1. ✅ **`settings/upload_config.php`** - NEW: Auto-detection logic
2. ✅ **`settings/core.php`** - UPDATED: Uses upload_config.php
3. ✅ **`actions/upload_product_image_action.php`** - UPDATED: Saves correct path format
4. ✅ **View files** - Already use `normalize_image_path()` ✨

---

## Quick Reference

### Local Development
- No setup needed
- Works automatically with XAMPP

### Live Server Deployment
```bash
# 1. Upload files
# 2. Create uploads directory
mkdir ~/uploads
chmod 775 ~/uploads

# 3. Configure web server (if using Option B)
# Add Alias to Apache config

# 4. Test
# Upload image via admin panel
# View in shop page
```

### Path Formats
| Environment | DB Path | File Path | Web URL |
|------------|---------|-----------|---------|
| Local | `uploads/u1/p5/img.jpg` | `htdocs/project/uploads/u1/p5/img.jpg` | `localhost/project/uploads/u1/p5/img.jpg` |
| Live | `/uploads/u1/p5/img.jpg` | `/home/user/uploads/u1/p5/img.jpg` | `yourserver.com/uploads/u1/p5/img.jpg` |

---

## Support

If images still don't show after following this guide:

1. Check PHP error logs
2. Check Apache error logs
3. Verify file permissions (775 for directories, 664 for files)
4. Test with the `test_environment.php` file
5. Check browser console for 404 errors on image URLs

Good luck with your deployment! 🚀

