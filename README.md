# Perfume Shop - MVC E-Commerce Platform

A modern, fully-featured e-commerce platform for luxury perfume retail built with PHP MVC architecture, MySQL, and Tailwind CSS.

## Project Structure

\`\`\`
perfume-shop/
├── public/                 # Customer-facing pages
│   ├── register.php       # User registration
│   ├── login.php          # User login
│   ├── shop.php           # Product listing
│   ├── cart.php           # Shopping cart
│   ├── checkout.php       # Checkout process
│   ├── orders.php         # Order history
│   └── js/                # JavaScript files
│       ├── category.js    # Category management
│       └── perfume.js     # Perfume management
├── admin/                  # Admin pages
│   ├── category.php       # Category management
│   └── perfume.php        # Perfume management
├── actions/               # AJAX endpoints
│   ├── register_customer_action.php
│   ├── login_action.php
│   ├── logout.php
│   ├── fetch_category_action.php
│   ├── add_category_action.php
│   ├── update_category_action.php
│   ├── delete_category_action.php
│   ├── fetch_perfume_action.php
│   ├── add_perfume_action.php
│   ├── update_perfume_action.php
│   ├── delete_perfume_action.php
│   ├── add_to_cart_action.php
│   ├── remove_from_cart_action.php
│   ├── update_cart_action.php
│   ├── checkout_action.php
│   └── review_actions.php
├── controllers/           # Business logic
│   ├── customer_controller.php
│   ├── category_controller.php
│   ├── perfume_controller.php
│   ├── cart_controller.php
│   ├── order_controller.php
│   └── review_controller.php
├── classes/              # Data access layer
│   ├── customer_class.php
│   ├── category_class.php
│   ├── perfume_class.php
│   ├── cart_class.php
│   ├── order_class.php
│   └── review_class.php
├── settings/             # Configuration
│   ├── db_cred.php      # Database credentials
│   ├── db_class.php     # Database helper class
│   └── core.php         # Core functions
├── db/                   # Database files
│   ├── perfume_shop_schema.sql
│   └── newsletter_table.sql
├── index.php            # Landing page
├── subscribe.php        # Newsletter subscription
└── README.md           # This file
\`\`\`

## Features

### Customer Features
- User registration and authentication
- Browse products by category
- Add products to cart
- Checkout and order placement
- View order history
- Leave product reviews

### Admin Features
- Manage product categories
- Add, edit, and delete products
- View all orders
- Manage order status
- Moderate customer reviews

### Technical Features
- MVC architecture with strict separation of concerns
- Prepared statements for SQL injection prevention
- Password hashing with bcrypt
- Session-based authentication
- AJAX-based interactions
- Responsive design with Tailwind CSS
- RESTful JSON API endpoints

## Database Setup

1. Create a database named `dbforlab` in MySQL
2. Run the SQL schema files in order:
   \`\`\`bash
   mysql -u root -p dbforlab < db/perfume_shop_schema.sql
   mysql -u root -p dbforlab < db/newsletter_table.sql
   \`\`\`

## Configuration

Update database credentials in `settings/db_cred.php`:

\`\`\`php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dbforlab');
define('DB_PORT', 3306);
\`\`\`

## Authentication

### User Roles
- **Customer (role = 2)**: Can browse products, add to cart, checkout, and leave reviews
- **Admin (role = 1)**: Can manage products, categories, and orders

### Session Management
- Sessions are stored in `$_SESSION['customer']` with user ID, name, email, and role
- Use `is_logged_in()` to check if user is authenticated
- Use `is_admin()` to check if user has admin privileges
- Use `require_login()` to protect pages requiring authentication
- Use `require_admin()` to protect admin pages

## API Endpoints

### Authentication
- `POST /actions/register_customer_action.php` - Register new user
- `POST /actions/login_action.php` - Login user
- `GET /actions/logout.php` - Logout user

### Categories
- `GET /actions/fetch_category_action.php` - Get all categories (admin only)
- `POST /actions/add_category_action.php` - Add category (admin only)
- `POST /actions/update_category_action.php` - Update category (admin only)
- `POST /actions/delete_category_action.php` - Delete category (admin only)

### Perfumes
- `GET /actions/fetch_perfume_action.php` - Get all perfumes (admin only)
- `POST /actions/add_perfume_action.php` - Add perfume (admin only)
- `POST /actions/update_perfume_action.php` - Update perfume (admin only)
- `POST /actions/delete_perfume_action.php` - Delete perfume (admin only)

### Cart
- `POST /actions/add_to_cart_action.php` - Add item to cart
- `POST /actions/remove_from_cart_action.php` - Remove item from cart
- `POST /actions/update_cart_action.php` - Update item quantity

### Orders
- `POST /actions/checkout_action.php` - Place order

## Usage

### For Customers
1. Register at `/public/register.php`
2. Login at `/public/login.php`
3. Browse products at `/public/shop.php`
4. Add items to cart and checkout
5. View orders at `/public/orders.php`

### For Admins
1. Login with admin account
2. Access admin panel at `/admin/category.php` or `/admin/perfume.php`
3. Manage categories and products
4. View and update orders

## Security Features

- Prepared statements prevent SQL injection
- Password hashing with bcrypt
- Input sanitization with `sanitize()` function
- CSRF protection ready (can be enhanced)
- Role-based access control
- Session-based authentication

## Future Enhancements

- Email notifications for orders
- Payment gateway integration (Stripe, PayPal)
- Advanced search and filtering
- Product recommendations
- Wishlist functionality
- Admin analytics dashboard
- Email marketing integration
- Two-factor authentication
- Product ratings and reviews moderation

## Support

For issues or questions, please contact the development team.

## License

All rights reserved. Essence Luxury Fragrances.
