# Image Upload - Simple Solution

## How It Works

Your image upload system uses **simple relative paths** that work on both local and live servers.

### Upload Options

**Single Image Upload** (`upload_product_image_action.php`):
- Upload one image at a time
- Saves to product-specific folder: `uploads/u{user_id}/p{product_id}/`

**Bulk Image Upload** (`bulk_upload_product_images_action.php`):
- Upload multiple images in ONE request
- Saves to temp folder: `uploads/u{user_id}/temp/`
- Faster and more efficient for multiple files

### Upload Process

1. **User uploads image(s)** via admin panel
2. **File saved to**: `uploads/u{user_id}/p{product_id}/filename.jpg` OR `uploads/u{user_id}/temp/filename.jpg`
3. **Database stores**: `uploads/u1/p5/image.jpg` (relative path from project root)

### Display Process

Files at different levels use appropriate relative paths:

**From root level files** (`index.php`):
```php
$image_path = $perfume['image'];  // uploads/u1/p5/image.jpg
```

**From `/public/` files** (`shop.php`, `cart.php`):
```php
$image_path = '../' . $perfume['image'];  // ../uploads/u1/p5/image.jpg
```

## File Structure

```
project/
├── index.php                    (uses: uploads/...)
├── uploads/                     ← Images stored here
│   └── u1/
│       └── p5/
│           └── image.jpg
└── public/
    └── shop.php                 (uses: ../uploads/...)
```

## Deployment

### Local (XAMPP):
1. Files in: `/Applications/XAMPP/.../project/`
2. Uploads in: `project/uploads/`
3. Works ✅

### Live Server:
1. Files in: `public_html/project/`
2. Uploads in: `public_html/project/uploads/`
3. Set folder permissions: `chmod 775 uploads/`
4. Works ✅

## Why It Works

- ✅ Same folder structure on both environments
- ✅ Simple relative paths (`../uploads/...`)
- ✅ No environment detection needed
- ✅ No complex configuration

**That's it!** Simple and effective. 🎉

