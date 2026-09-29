CREATE DATABASE IF NOT EXISTS findit_campus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE findit_campus;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    item_type ENUM('lost', 'found') NOT NULL,
    title VARCHAR(140) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(180) NOT NULL,
    item_date DATE NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    status ENUM('open', 'returned') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_items_search (status, item_type, category_id, item_date),
    INDEX idx_items_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS claims (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    proof TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_claims_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_claims_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_claim_user_item (item_id, user_id),
    INDEX idx_claims_status (status, created_at)
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (name) VALUES
    ('Electronics'), ('Clothing'), ('Books & papers'), ('Keys'), ('Bags'),
    ('Cards & IDs'), ('Accessories'), ('Other');
