-- Drop existing tables in reverse order of foreign key dependencies to avoid constraint errors
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- 1. Create Users Table (for Authentication & Role-based access)
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create Products Table (Catalog)
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    tag VARCHAR(100) DEFAULT '',
    description TEXT,
    image VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Orders Table (Customer purchases)
CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    customer_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'M-Pesa',
    items TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insert Default Admin Account (Email: admin@scoopswirl.com | Password: admin123)
INSERT INTO users (name, email, password, role) 
VALUES ('System Admin', 'admin@scoopswirl.com', '$2y$10$wT8mI8m2oG7L3vN8a7p.HeN5fR9/G5N.yX9q6O7A2b3c4d5e6f7g8', 'admin');