CREATE DATABASE IF NOT EXISTS ulanalert; USE ulanalert;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(120) UNIQUE,password VARCHAR(255),role ENUM('user','admin') DEFAULT 'user');
CREATE TABLE reports(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,location VARCHAR(120),severity ENUM('Light','Heavy','Flooded'),notes VARCHAR(255),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id));
CREATE TABLE confirmations(id INT AUTO_INCREMENT PRIMARY KEY,report_id INT,user_id INT,UNIQUE KEY one_each(report_id,user_id),FOREIGN KEY(report_id) REFERENCES reports(id) ON DELETE CASCADE);
