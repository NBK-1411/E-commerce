# 🎯 Quick Reference - No Admin Access Deployment

## Your Situation
✅ Uploads folder in `public_html`  
❌ No admin/root access  
✅ Have FTP/SFTP access  
✅ **Solution ready to deploy!**

---

## 3-Step Deployment

### 1️⃣ Upload via FTP
```
Using FileZilla/WinSCP:
→ Connect to server
→ Navigate to public_html/your-project/
→ Upload ALL project files
→ Including uploads/ folder
```

### 2️⃣ Set Permissions via FTP
```
→ Right-click uploads folder
→ File Permissions / Properties
→ Set to 775 or 777
→ Apply to subdirectories
```

### 3️⃣ Test
```
Visit: yourserver.com/your-project/test_upload_write.php
Should see: ✅ All Tests Passed!
```

---

## Testing Checklist

| Test | URL | Expected Result |
|------|-----|-----------------|
| **Write Test** | `test_upload_write.php` | ✅ All Tests Passed |
| **Config Test** | `test_environment.php` | ✅ Directory Writable: YES |
| **Upload Test** | Admin Panel → Upload Image | Image saved successfully |
| **Display Test** | Shop Page | Images display correctly |

---

## Common Issues & Quick Fixes

### ❌ "Not writable"
**Fix:** Set permissions to `777` via FTP

### ❌ Images upload but 403 error
**Fix:** Delete `.htaccess` from uploads folder

### ❌ Can't change permissions
**Fix:** Use cPanel File Manager OR contact support

### ❌ 404 on images
**Fix:** Check file actually uploaded via FTP

---

## FTP Permission Setting

### FileZilla:
1. Right-click folder
2. File Permissions
3. Numeric: `775` or `777`
4. ✅ Recurse into subdirectories
5. OK

### WinSCP:
1. Right-click folder
2. Properties
3. Octal: `0775` or `0777`
4. Set group and others recursively
5. OK

### cPanel File Manager:
1. Navigate to folder
2. Right-click → Change Permissions
3. Check boxes for Read/Write/Execute
4. Owner: ✅✅✅ Group: ✅✅✅ World: ✅◻️✅
5. Apply to subdirectories
6. Save

---

## What You CAN Do (No Admin)

✅ Upload files via FTP  
✅ Set folder permissions (775/777)  
✅ Create/edit .htaccess  
✅ Delete files you own  
✅ Use cPanel tools  
✅ Contact hosting support

## What You DON'T Need

❌ SSH access (nice to have, not required)  
❌ Root/sudo access  
❌ Apache configuration  
❌ Server-wide settings  
❌ Admin panel access

---

## Files to Upload

```
✅ Upload ALL of these:

/actions/
/admin/
/classes/
/controllers/
/db/
/public/
/settings/
  ├── upload_config.php     ← NEW
  ├── core.php              ← UPDATED
  └── ...
/uploads/                   ← Empty folder OK
/index.php
/test_environment.php       ← NEW (for testing)
/test_upload_write.php      ← NEW (for testing)
... (all other files)
```

---

## Testing Commands

### Manual Test via FTP:
1. Upload a test image: `uploads/test.jpg`
2. Visit: `yourserver.com/your-project/uploads/test.jpg`
3. Can you see it? → ✅ Working!

### Check via Browser:
```
test_upload_write.php → Tests write permissions
test_environment.php  → Tests configuration
```

---

## Contact Support Template

If permissions won't work:

```
Subject: Need Write Permissions on Upload Folder

Hello,

I'm running a PHP application that needs to save 
uploaded images. Can you please ensure this folder 
is writable by PHP:

~/public_html/your-project-name/uploads/

Permissions needed: 775 or 777

Thank you!
```

**They can fix this without giving you admin access!**

---

## Security via .htaccess

Create `uploads/.htaccess` via FTP:

```apache
# Block PHP execution
<FilesMatch "\.(php|phtml)$">
    Deny from all
</FilesMatch>

# Allow only images
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Allow from all
</FilesMatch>

# No directory listing
Options -Indexes
```

**You can create this in your FTP client!**
- Create new file
- Name it `.htaccess`
- Paste above content
- Save

---

## Troubleshooting Steps

### Step 1: Basic Check
```
1. Can you access the site? → Check FTP uploaded correctly
2. Does uploads/ folder exist? → Check via FTP
3. Can you open an image URL directly? → Test web access
```

### Step 2: Permission Check
```
1. Open FTP client
2. Navigate to uploads/
3. Right-click → Properties
4. Check permissions are 775 or 777
5. If not, change them
```

### Step 3: Test Files
```
1. Visit test_upload_write.php
2. Read the test results
3. Follow the specific fixes shown
```

### Step 4: Get Help
```
1. Check cPanel error logs
2. Contact hosting support
3. They can check server-side issues
```

---

## Success Indicators

### ✅ Working Correctly:
- `test_upload_write.php` → All tests passed
- `test_environment.php` → Directory writable: YES
- Upload via admin → Success message
- View on shop → Images display
- Browser console → No 404 errors

### ❌ Not Working:
- Test pages show errors → Fix permissions
- Upload fails → Check write permissions
- Images don't display → Check file exists via FTP
- 403 Forbidden → Check .htaccess

---

## Why This Works

Your setup is **perfect for no-admin deployment**:

```
✅ Uploads in public_html → Web accessible by default
✅ Relative paths → No absolute paths needed
✅ Auto-detection → No manual config needed
✅ FTP permissions → No SSH needed
✅ Self-contained → No server changes needed
```

---

## Quick Wins

### Before Deploying:
- [x] Code ready (already done!)
- [ ] Have FTP credentials
- [ ] Know project URL

### After Deploying:
- [ ] Files uploaded
- [ ] Permissions set to 775/777
- [ ] Tests show green
- [ ] Upload works
- [ ] Display works

**Total time: ~10 minutes** ⏱️

---

## Remember

1. **You don't need admin access** - FTP is enough!
2. **Permissions fix 90% of issues** - Try 777 if 775 fails
3. **Test files will guide you** - They show exactly what's wrong
4. **Support can help** - They don't need to give you admin
5. **It's simple** - Upload, permissions, test, done!

---

## Next Step

📖 **Read:** `NO_ADMIN_DEPLOYMENT.md` for full details

🚀 **Then:** Upload your files and set permissions

🧪 **Test:** Visit the test files

🎉 **Done:** Your images will work!

---

**You've got this!** 💪

