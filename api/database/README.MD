
# Milan Booster Database

This directory contains the SQL schema for the Milan Booster application.

## Setup Instructions

1. Create a database named `milan_booster`
2. Import the `milan_booster.sql` file into your database
3. Configure the database connection settings in `../includes/db.php`

## Default Admin Credentials

- Username: admin
- Password: admin123

## Tables Structure

### Users

- id: Primary key
- username: Unique username
- email: User email address
- password: Hashed password
- balance: User balance (decimal)
- created_at: Account creation timestamp
- is_admin: Boolean flag for admin privileges

### Categories

- id: Primary key
- name: Category name (YouTube, Facebook, etc.)

### Services

- id: Primary key
- category_id: Foreign key referencing categories
- name: Service name
- price_per_1000: Price per 1000 units
- min_quantity: Minimum order quantity
- max_quantity: Maximum order quantity

### Orders

- id: Primary key
- user_id: Foreign key referencing users
- service_id: Foreign key referencing services
- link: URL for the order
- quantity: Order quantity
- price: Total price
- status: Order status (pending, processing, completed, cancelled)
- created_at: Order creation timestamp
