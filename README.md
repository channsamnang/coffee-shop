# Coffee Shop Management System

A complete PHP and MySQL-based Coffee Shop Management System with role-based access control.

## Features

### Admin Features
- Dashboard with sales analytics and statistics
- Manage coffee items (add, edit, delete)
- View and manage all orders
- Update order status (Pending, Preparing, Ready, Delivered, Cancelled)
- Manage users (block, unblock, delete)
- View sales reports and top-selling items

### User Features
- User registration and login
- Browse coffee menu with categories and search
- Add items to cart
- Place orders with delivery address
- View order history
- Track order status

## Installation

1. **Database Setup**
   - Create a MySQL database named `coffee_shop`
   - Update database credentials in `config/database.php`
   - Run the SQL scripts in the `scripts` folder:
     - `01-create-database.sql` - Creates tables
     - `02-seed-data.sql` - Seeds initial data

2. **Server Setup**
   - Place all files in your web server directory (e.g., `htdocs` for XAMPP)
   - Ensure PHP 7.4+ and MySQL 5.7+ are installed
   - Enable PDO MySQL extension in PHP

3. **Default Admin Account**
   - Email: `admin@coffeeshop.com`
   - Password: `admin123`

## Project Structure

\`\`\`
/
├── api/                    # API endpoints
│   ├── auth/              # Authentication APIs
│   ├── coffee/            # Coffee items APIs
│   ├── orders/            # Orders APIs
│   ├── users/             # User management APIs
│   └── analytics/         # Analytics APIs
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── auth.php           # Authentication helpers
├── public/css/            # Stylesheets
├── scripts/               # SQL scripts
├── admin/                 # Admin pages
├── user/                  # User pages
├── login.php              # Login page
├── register.php           # Registration page
└── index.php              # Entry point
\`\`\`

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Authentication**: Session-based with password hashing

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using PDO prepared statements
- Role-based access control
- Session management
- XSS protection with `htmlspecialchars()`

## API Endpoints

### Authentication
- `POST /api/auth/login.php` - User login
- `POST /api/auth/register.php` - User registration
- `GET /api/auth/logout.php` - User logout

### Coffee Items
- `GET /api/coffee/get-items.php` - Get all items
- `POST /api/coffee/manage-item.php` - Create item (Admin)
- `PUT /api/coffee/manage-item.php` - Update item (Admin)
- `DELETE /api/coffee/manage-item.php` - Delete item (Admin)

### Orders
- `POST /api/orders/create-order.php` - Create order
- `GET /api/orders/get-orders.php` - Get orders
- `PUT /api/orders/update-status.php` - Update order status (Admin)

### Users
- `GET /api/users/manage-user.php` - Get all users (Admin)
- `PUT /api/users/manage-user.php` - Update user (Admin)
- `DELETE /api/users/manage-user.php` - Delete user (Admin)

### Analytics
- `GET /api/analytics/get-stats.php` - Get dashboard statistics (Admin)

## License

MIT License


####
# Coffee Shop Management System

A complete PHP and MySQL-based Coffee Shop Management System with role-based access control.

## Features

### Admin Features
- Dashboard with sales analytics and statistics
- Manage coffee items (add, edit, delete)
- View and manage all orders
- Update order status (Pending, Preparing, Ready, Delivered, Cancelled)
- Manage users (block, unblock, delete)
- View sales reports and top-selling items

### User Features
- User registration and login
- Browse coffee menu with categories and search
- Add items to cart
- Place orders with delivery address
- View order history
- Track order status

## Installation

1. **Database Setup**
   - Create a MySQL database named `coffee_shop`
   - Update database credentials in `config/database.php`
   - Run the SQL scripts in the `scripts` folder:
     - `01-create-database.sql` - Creates tables
     - `02-seed-data.sql` - Seeds initial data

2. **Server Setup**
   - Place all files in your web server directory (e.g., `htdocs` for XAMPP)
   - Ensure PHP 7.4+ and MySQL 5.7+ are installed
   - Enable PDO MySQL extension in PHP

3. **Default Admin Account**
   - Email: `admin@coffeeshop.com`
   - Password: `admin123`

## Project Structure

\`\`\`
/
├── api/                    # API endpoints
│   ├── auth/              # Authentication APIs
│   ├── coffee/            # Coffee items APIs
│   ├── orders/            # Orders APIs
│   ├── users/             # User management APIs
│   └── analytics/         # Analytics APIs
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── auth.php           # Authentication helpers
├── public/css/            # Stylesheets
├── scripts/               # SQL scripts
├── admin/                 # Admin pages
├── user/                  # User pages
├── login.php              # Login page
├── register.php           # Registration page
└── index.php              # Entry point
\`\`\`

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Authentication**: Session-based with password hashing

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using PDO prepared statements
- Role-based access control
- Session management
- XSS protection with `htmlspecialchars()`

## API Endpoints

### Authentication
- `POST /api/auth/login.php` - User login
- `POST /api/auth/register.php` - User registration
- `GET /api/auth/logout.php` - User logout

### Coffee Items
- `GET /api/coffee/get-items.php` - Get all items
- `POST /api/coffee/manage-item.php` - Create item (Admin)
- `PUT /api/coffee/manage-item.php` - Update item (Admin)
- `DELETE /api/coffee/manage-item.php` - Delete item (Admin)

### Orders
- `POST /api/orders/create-order.php` - Create order
- `GET /api/orders/get-orders.php` - Get orders
- `PUT /api/orders/update-status.php` - Update order status (Admin)

### Users
- `GET /api/users/manage-user.php` - Get all users (Admin)
- `PUT /api/users/manage-user.php` - Update user (Admin)
- `DELETE /api/users/manage-user.php` - Delete user (Admin)

### Analytics
- `GET /api/analytics/get-stats.php` - Get dashboard statistics (Admin)

## License

MIT License
