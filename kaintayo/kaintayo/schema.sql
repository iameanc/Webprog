CREATE DATABASE IF NOT EXISTS kaintayo; USE kaintayo;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(120) UNIQUE,password VARCHAR(255),role ENUM('user','admin') DEFAULT 'user');
CREATE TABLE menu_items(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),category VARCHAR(50),price DECIMAL(8,2),available TINYINT DEFAULT 1);
CREATE TABLE orders(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,total DECIMAL(9,2),pickup_time VARCHAR(20),status ENUM('pending','preparing','ready') DEFAULT 'pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id));
CREATE TABLE order_items(id INT AUTO_INCREMENT PRIMARY KEY,order_id INT,menu_item_id INT,qty INT,price DECIMAL(8,2),FOREIGN KEY(order_id) REFERENCES orders(id));
INSERT INTO menu_items(name,category,price) VALUES('Adobo Meal','Rice Meals',75),('Sinigang','Rice Meals',85),('Turon','Snacks',20),('Iced Tea','Drinks',30);
