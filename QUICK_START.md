# 🚀 Quick Start - Displaying Images on Live Server

## TL;DR - What I Did

I've configured your system to **automatically detect** whether it's running on local XAMPP or your live server and adjust image paths accordingly.

**No code changes needed when deploying!** ✨

---

## For Your Live Server - Do This:

### Step 1: Upload Your Files
Upload everything to your server's `public_html` directory.

### Step 2: Create Uploads Directory

**Option A - Simple (Inside public_html):**
```bash
cd ~/public_html/your-project-folder/
mkdir uploads
chmod 775 uploads
```

**Option B - Secure (Outside public_html - Recommended):**
```bash
cd ~
mkdir uploads
chmod 775 uploads
```

Then add to your Apache config or `.htaccess`:
```apache
Alias /uploads /home/username/uploads

<Directory /home/username/uploads>
    Options -Indexes
    AllowOverride None
    Require all granted
</Directory>
```

### Step 3: Test It
Visit: `http://yourserver.com/your-project/test_environment.php`

You should see all green checkmarks ✅

---

## How It Works

### Local Development (XAMPP)
- Images saved to: `uploads/u1/p5/image.jpg` (relative)
- Database stores: `uploads/u1/p5/image.jpg`
- HTML displays: `http://localhost/project/uploads/u1/p5/image.jpg`

### Live Server
- Images saved to: `/uploads/u1/p5/image.jpg` (absolute)
- Database stores: `/uploads/u1/p5/image.jpg`
- HTML displays: `http://yourserver.com/uploads/u1/p5/image.jpg`

**The system automatically chooses the correct format!**

---

## Files I Modified

1. ✅ **Created:** `settings/upload_config.php` - Auto-detection logic
2. ✅ **Updated:** `settings/core.php` - Uses new config
3. ✅ **Updated:** `actions/upload_product_image_action.php` - Saves correct paths
4. ✅ **Created:** `test_environment.php` - Test your setup
5. ✅ **Created:** `LIVE_SERVER_DEPLOYMENT_GUIDE.md` - Detailed instructions

---

## Testing Checklist

- [ ] Run `test_environment.php` - all should be green
- [ ] Upload a product image via admin panel
- [ ] View the product on shop page
- [ ] Image should display correctly
- [ ] Check browser console - no 404 errors

---

## Troubleshooting

**Images not showing?**

1. Check `test_environment.php` first
2. Verify uploads directory has 775 permissions
3. Check PHP error log: `tail -f ~/public_html/error_log`
4. Test file access: Create `~/uploads/test.txt` and visit `http://yourserver.com/uploads/test.txt`

**Still stuck?**

Read the full guide: `LIVE_SERVER_DEPLOYMENT_GUIDE.md`

---

## Quick Commands Reference

```bash
# Create uploads directory
mkdir ~/uploads
chmod 775 ~/uploads

# Check permissions
ls -la ~/uploads

# Test write access
touch ~/uploads/test.txt
echo "success" > ~/uploads/test.txt

# Check if PHP can access it
# Visit: http://yourserver.com/uploads/test.txt
```

---

## What The README Teaches

The attached `UPLOAD_ENVIRONMENT_README.md` explains the **concept** of dual-environment setup.

I've **implemented** that concept for you. Your code now:
- ✅ Auto-detects environment
- ✅ Uses correct paths automatically
- ✅ Works locally AND on live server
- ✅ Stores paths correctly in database
- ✅ Displays images correctly in HTML

**Just deploy and test!** 🎉

