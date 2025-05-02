
-- Create database
CREATE DATABASE IF NOT EXISTS milan_booster;
USE milan_booster;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin TINYINT(1) DEFAULT 0
);

-- Services categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price_per_1000 DECIMAL(10,2) NOT NULL,
    min_quantity INT NOT NULL DEFAULT 100,
    max_quantity INT NOT NULL DEFAULT 10000,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    link VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, is_admin, balance) 
VALUES ('admin', 'admin@milanbooster.com', '$2y$10$XK1OlO/CTQW7hgItPYC4YOaLxbkh4yGaVoFJVXM5ciRPFylbSA9SG', 1, 1000.00);

-- Insert categories
INSERT INTO categories (name) VALUES 
('YouTube'), 
('Facebook'), 
('TikTok'), 
('Instagram');

-- Insert services
-- YouTube services
INSERT INTO services (category_id, name, price_per_1000, min_quantity, max_quantity) VALUES 
(1, 'YouTube Views', 5.00, 100, 100000),
(1, 'YouTube Subscribers', 20.00, 100, 10000),
(1, 'YouTube Likes', 10.00, 100, 50000);

-- Facebook services
INSERT INTO services (category_id, name, price_per_1000, min_quantity, max_quantity) VALUES 
(2, 'Facebook Views', 3.00, 100, 100000),
(2, 'Facebook Likes', 8.00, 100, 50000),
(2, 'Facebook Followers', 15.00, 100, 10000);

-- TikTok services
INSERT INTO services (category_id, name, price_per_1000, min_quantity, max_quantity) VALUES 
(3, 'TikTok Views', 4.00, 100, 100000),
(3, 'TikTok Likes', 7.00, 100, 50000),
(3, 'TikTok Followers', 18.00, 100, 10000);

-- Instagram services
INSERT INTO services (category_id, name, price_per_1000, min_quantity, max_quantity) VALUES 
(4, 'Instagram Followers', 12.00, 100, 50000),
(4, 'Instagram Likes', 6.00, 100, 100000),
(4, 'Instagram Views', 3.50, 100, 500000);
