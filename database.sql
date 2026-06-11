-- Junoob Al Doha Trading Database Setup
CREATE DATABASE IF NOT EXISTS junoob_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE junoob_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255),
    description TEXT,
    description_ar TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    category VARCHAR(100),
    image_url TEXT,
    stock INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'cod',
    address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT
);

-- Promo Codes Table
CREATE TABLE IF NOT EXISTS promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type ENUM('percentage','fixed') DEFAULT 'fixed',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    expires_at DATE
);

-- Insert Default Settings
INSERT IGNORE INTO settings (key_name, value) VALUES
('store_name', 'Junoob Al Doha Trading'),
('store_email', 'info@junoobdoha.com'),
('store_phone', '+97455123456'),
('store_whatsapp', '97455123456'),
('store_address', 'Doha, Qatar'),
('shipping_fee', '10'),
('free_shipping_above', '200'),
('facebook_url', ''),
('instagram_url', '');

-- Insert Default Admin
INSERT IGNORE INTO users (name, email, phone, password, role) VALUES
('Admin', 'admin@junoob.com', '+97455123456', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Products
INSERT IGNORE INTO products (name, name_ar, description, price, original_price, category, stock, is_featured) VALUES
('Premium Dates', 'تمر ممتاز', 'High quality dates from Qatar', 45.00, 60.00, 'Food', 100, 1),
('Arabic Coffee', 'قهوة عربية', 'Traditional Arabic coffee blend', 35.00, NULL, 'Beverages', 50, 1),
('Saffron', 'زعفران', 'Pure Iranian saffron', 120.00, 150.00, 'Spices', 30, 0);

-- Insert Sample Promo Codes
INSERT IGNORE INTO promo_codes (code, discount_type, discount_value, min_order) VALUES
('WELCOME10', 'percentage', 10, 0),
('SAVE20', 'fixed', 20, 100);
