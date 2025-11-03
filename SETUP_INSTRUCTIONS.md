# Setup Instructions

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Composer (optional, for dependency management)

## Installation Steps

### 1. Database Setup
\`\`\`bash
# Create database
mysql -u root -p -e "CREATE DATABASE dbforlab;"

# Import schema
mysql -u root -p dbforlab < db/perfume_shop_schema.sql
mysql -u root -p dbforlab < db/newsletter_table.sql
\`\`\`

### 2. Configure Database Connection
Edit `settings/db_cred.php` with your database credentials:
\`\`\`php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'dbforlab');
define('DB_PORT', 3306);
\`\`\`

### 3. Create Admin User (Optional)
\`\`\`sql
INSERT INTO customers (name, email, password, country, city, contact, role) 
VALUES ('Admin', 'admin@example.com', '$2y$10$...', 'USA', 'New York', '1234567890', 1);
\`\`\`

Note: Use PHP to hash the password:
\`\`\`php
echo password_hash('your_password', PASSWORD_BCRYPT);
\`\`\`

### 4. Set File Permissions
\`\`\`bash
chmod 755 public/
chmod 755 admin/
chmod 755 actions/
\`\`\`

### 5. Start Development Server
\`\`\`bash
# Using PHP built-in server
php -S localhost:8000

# Or use your web server (Apache, Nginx, etc.)
\`\`\`

### 6. Access the Application
- Landing page: `http://localhost:8000/index.php`
- Shop: `http://localhost:8000/public/shop.php`
- Register: `http://localhost:8000/public/register.php`
- Admin: `http://localhost:8000/admin/category.php` (requires admin login)

## Troubleshooting

### Database Connection Error
- Check database credentials in `settings/db_cred.php`
- Ensure MySQL server is running
- Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Session Issues
- Ensure PHP session.save_path is writable
- Check PHP session settings in php.ini

### File Upload Issues
- Create `uploads/` directory with write permissions
- Check PHP upload_max_filesize and post_max_size settings

## Testing

### Test User Accounts
Create test accounts for different roles:

**Customer Account:**
\`\`\`sql
INSERT INTO customers (name, email, password, country, city, contact, role) 
VALUES ('John Doe', 'customer@example.com', '$2y$10$...', 'USA', 'New York', '1234567890', 2);
\`\`\`

**Admin Account:**
\`\`\`sql
INSERT INTO customers (name, email, password, country, city, contact, role) 
VALUES ('Admin User', 'admin@example.com', '$2y$10$...', 'USA', 'New York', '1234567890', 1);
\`\`\`

### Sample Data
Add sample categories and perfumes:
\`\`\`sql
INSERT INTO categories (name) VALUES ('Floral'), ('Woody'), ('Oriental'), ('Citrus');

INSERT INTO perfumes (name, brand, category_id, price, stock, description) 
VALUES ('Midnight Rose', 'Essence', 1, 145.00, 50, 'A beautiful floral fragrance');
\`\`\`

## Performance Optimization

- Enable database query caching
- Use prepared statements (already implemented)
- Implement pagination for product listings
- Add database indexes (already included in schema)
- Use CDN for static assets
- Enable gzip compression

## Deployment

### Production Checklist
- [ ] Update database credentials
- [ ] Set appropriate file permissions
- [ ] Enable HTTPS
- [ ] Configure error logging
- [ ] Set up automated backups
- [ ] Configure email service for notifications
- [ ] Test all payment integrations
- [ ] Set up monitoring and alerts
- [ ] Configure firewall rules
- [ ] Review security settings

### Deployment Steps
1. Clone repository to production server
2. Update database credentials
3. Run database migrations
4. Set file permissions
5. Configure web server
6. Set up SSL certificate
7. Configure email service
8. Test all functionality

## Support

For technical support, contact: support@example.com
