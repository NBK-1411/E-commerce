# Remote Upload Server Configuration

## ✅ What Changed

Your image upload system now uploads files to your **remote server** instead of storing them locally.

### Remote Upload Server:
**URL:** `http://169.239.251.102:442/~nana.hayford/upload.php`

---

## 📁 Files Updated

### 1. Single Upload (`actions/upload_product_image_action.php`)
- Now uses cURL to send files to remote server
- Stores remote URL in database
- Format: `http://169.239.251.102:442/~nana.hayford/uploads/filename.jpg`

### 2. Bulk Upload (`actions/bulk_upload_product_images_action.php`)
- Uploads multiple files to remote server in one batch
- Each file sent via cURL
- All remote URLs returned

---

## 🔄 How It Works

### Upload Flow:

```
User uploads image
    ↓
Your PHP receives file
    ↓
cURL sends file to remote server
    ↓
Remote server saves file
    ↓
Your PHP stores remote URL in database
    ↓
Images display from remote server
```

### Code Example:

```php
// Create cURL file upload
$cfile = new CURLFile($file['tmp_name'], $mime_type, $file['name']);

// Send to remote server
curl_setopt($ch, CURLOPT_URL, 'http://169.239.251.102:442/~nana.hayford/upload.php');
curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $cfile]);
$response = curl_exec($ch);

// Store remote URL
$db_path = 'http://169.239.251.102:442/~nana.hayford/uploads/filename.jpg';
```

---

## 📊 Database Storage

### Before (Local):
```
product_image: "uploads/u1/p5/image.jpg"
```

### After (Remote):
```
product_image: "http://169.239.251.102:442/~nana.hayford/uploads/image.jpg"
```

---

## 🎨 Display in HTML

### Your View Files:
No changes needed! The `normalize_image_path()` function already handles full URLs:

```php
// In index.php, shop.php, etc.
<img src="<?php echo htmlspecialchars($perfume['image']); ?>">

// If $perfume['image'] is a full URL, it displays directly
// Output: <img src="http://169.239.251.102:442/~nana.hayford/uploads/image.jpg">
```

---

## ✅ Benefits

### Remote Storage:
- ✅ No local disk space used
- ✅ Centralized file management
- ✅ Can access files from anywhere
- ✅ Easier to scale

### Your Setup:
- ✅ All uploads go to one server
- ✅ Database stores full URLs
- ✅ Images accessible from any location
- ✅ No path conversion needed

---

## 🧪 Testing

### Test Single Upload:
1. Go to admin panel → Add Product
2. Upload an image
3. Check database → Should have full URL
4. View product → Image should display from remote server

### Test Bulk Upload:
1. Go to `admin/bulk_upload.php`
2. Select multiple images
3. Click "Upload All Images"
4. All should upload to remote server
5. Check response → Should show remote URLs

### Verify Remote Storage:
Visit: `http://169.239.251.102:442/~nana.hayford/upload.php`

You should see your uploaded files listed!

---

## 🔧 Configuration

### Remote Server URL:
Currently hardcoded in both upload actions:
```php
$remote_upload_url = 'http://169.239.251.102:442/~nana.hayford/upload.php';
```

### To Change URL:
Edit both files:
- `actions/upload_product_image_action.php` (line 149)
- `actions/bulk_upload_product_images_action.php` (line 38)

---

## 🛠️ Requirements

### Your Server Needs:
- ✅ cURL extension enabled (usually enabled by default)
- ✅ Internet connection to reach remote server
- ✅ No local uploads folder needed

### Remote Server Needs:
- ✅ Accept POST file uploads
- ✅ Return HTTP 200 on success
- ✅ Store files in accessible location

---

## 🐛 Troubleshooting

### Error: "Failed to upload to remote server"

**Check 1:** Is cURL enabled?
```php
<?php
if (function_exists('curl_version')) {
    echo "cURL is enabled";
} else {
    echo "cURL is NOT enabled";
}
?>
```

**Check 2:** Can you reach the remote server?
```bash
curl http://169.239.251.102:442/~nana.hayford/upload.php
```

**Check 3:** Check PHP error log for details

### Error: "HTTP 404" or "HTTP 500"

The remote server might be down or the URL is incorrect.

**Fix:** Verify the remote server URL is correct and accessible.

### Images not displaying

**Check:** View page source and look at image src:
```html
<img src="http://169.239.251.102:442/~nana.hayford/uploads/image.jpg">
```

Copy that URL and paste in browser. Does it load?
- **YES** → Problem is with HTML/CSS
- **NO** → File wasn't uploaded or URL is wrong

---

## 📝 Summary

### What You Have Now:

✅ **Single Upload:** Sends files to remote server via cURL  
✅ **Bulk Upload:** Sends multiple files to remote server  
✅ **Database:** Stores full remote URLs  
✅ **Display:** Images load from remote server  
✅ **No Local Storage:** All files on remote server  

### Your Remote Server:
- URL: `http://169.239.251.102:442/~nana.hayford/upload.php`
- Uploaded files visible at: `http://169.239.251.102:442/~nana.hayford/uploads/`

**Everything is configured and ready to use!** 🚀

---

## 💡 Next Steps

1. Test single image upload
2. Test bulk image upload
3. Verify images display correctly
4. Check remote server to see uploaded files

Your upload system is now fully integrated with your remote server! 🎉

