-- Database Schema for Petal Picks Flower Boutique

CREATE DATABASE IF NOT EXISTS flower_shop;
USE flower_shop;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100) NULL;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'Bouquets',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE products ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'Bouquets';

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) DEFAULT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20) DEFAULT NULL;

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Feedback Table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    rating INT NOT NULL DEFAULT 5,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Flower Products Catalog including Wedding Bouquets
INSERT INTO products (id, name, description, price, image_url, category) VALUES
(1, 'Passion Red Roses', 'Classic bouquet of fresh deep red velvet roses wrapped in premium craft paper.', 34.00, './image/red_roses.jpg', 'Roses'),
(2, 'Pure White Roses', 'Elegant arrangement of pure white roses symbolizing innocence and elegance.', 32.00, './image/white_roses.jpg', 'Roses'),
(3, 'Red & White Romance Mix', 'Stunning harmonious blend of vibrant red roses and soft white roses.', 36.00, './image/red_white_mix_roses.jpg', 'Roses'),
(4, 'Sunset Yellow & Red Tulips', 'Vibrant spring bouquet featuring bright yellow and deep red tulips.', 29.00, './image/yellow_red_tulips.jpg', 'Tulips'),
(5, 'Sacred Pink Lotus (Nelum)', 'Handpicked traditional pink lotus bouquet, symbolizing purity and devotion.', 28.00, './image/nelum_lotus.jpg', 'Traditional'),
(6, 'Royal Blue Water Lily (Nil Manel)', 'Exquisite blue water lily bouquet celebrating Sri Lanka\'s iconic national bloom.', 30.00, './image/nil_manel.jpg', 'Traditional'),
(7, 'Fresh White Olu Bouquet', 'Delicate white water lilies with lush green stems and subtle fragrant charm.', 24.00, './image/5.jpg', 'Traditional'),
(8, 'Exotic Red Anthurium', 'Long-lasting heart-shaped tropical red anthuriums wrapped for modern luxury.', 38.00, './image/10.jpg', 'Exotic'),
(9, 'Traditional Kandyan Bridal Bouquet', 'Handcrafted traditional Sri Lankan bridal bouquet with lotus, white roses, jasmine & gold trim.', 85.00, './image/nelum_lotus.jpg', 'Wedding Bouquets'),
(10, 'Western Cascading Bridal Bouquet', 'Luxurious Western waterfall bridal bouquet with white roses, calla lilies & baby\'s breath.', 95.00, './image/DEEEEEE.avif', 'Wedding Bouquets'),
(11, 'Boho Chic Western Bridal Bouquet', 'Modern Western bridal bouquet featuring blush roses, eucalyptus & pampas accents.', 80.00, './image/4.jpg', 'Wedding Bouquets'),
(12, 'Traditional Poruwa Bridesmaid Set', 'Matching bridesmaid bouquets with red roses, white jasmine & gold ribbons.', 65.00, './image/red_white_mix_roses.jpg', 'Wedding Bouquets'),
(13, 'Minimalist Western White Posy', 'Sleek Western bridal posy with white tulips, garden roses & ivory satin ribbon.', 70.00, './image/white_roses.jpg', 'Wedding Bouquets'),
(14, 'White Peony Delight', 'Elegant white peony bouquet layered with eucalyptus and lush greenery.', 25.00, './image/1.jpg', 'Bouquets'),
(15, 'Asters Floral Bouquet', 'A cheerful multi-colored asters arrangement perfect for birthdays & celebrations.', 30.00, './image/11.jpg', 'Bouquets'),
(16, 'Exotic Orchid Bouquet', 'Luxurious tropical orchids arranged in a premium satin wrapping.', 40.00, './image/14.jpg', 'Orchids')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), price=VALUES(price), image_url=VALUES(image_url), category=VALUES(category);

-- Default Admin Account: admin / admin123
INSERT INTO users (id, username, email, password, role) VALUES 
(1, 'admin', 'admin@petalpicks.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin')
ON DUPLICATE KEY UPDATE username=VALUES(username);
