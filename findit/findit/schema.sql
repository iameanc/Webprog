CREATE DATABASE IF NOT EXISTS findit; USE findit;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(120) UNIQUE,password VARCHAR(255),role ENUM('user','admin') DEFAULT 'user');
CREATE TABLE items(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,title VARCHAR(120),category VARCHAR(50),status ENUM('lost','found','returned'),location VARCHAR(120),photo VARCHAR(80),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id));
CREATE TABLE claims(id INT AUTO_INCREMENT PRIMARY KEY,item_id INT,user_id INT,proof TEXT,status ENUM('pending','approved','rejected') DEFAULT 'pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(item_id) REFERENCES items(id),FOREIGN KEY(user_id) REFERENCES users(id));
