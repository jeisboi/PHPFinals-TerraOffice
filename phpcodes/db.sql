CREATE DATABASE IF NOT EXISTS terraoffice_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE terraoffice_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','staff','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name, slug) VALUES
('Chairs','chairs'),
('Desks & Tables','desks-tables'),
('Storage','storage'),
('Accessories','accessories'),
('Electronics','electronics'),
('Office Supplies','office-supplies'),
('Lighting','lighting'),
('Ergonomic','ergonomic'),
('Décor & Comfort','decor-comfort'),
('Clearance','clearance');

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  category VARCHAR(120) NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  stock INT NOT NULL DEFAULT 0,
  image_filename VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO products (name, category, price, stock) VALUES
('Ergo Chair','Chairs',4500.00,10),
('Desk Lamp','Accessories',900.00,25),
('Wireless Mouse','Electronics',799.00,50);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  price_at DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_orderitems_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_orderitems_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;