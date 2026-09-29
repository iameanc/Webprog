CREATE DATABASE IF NOT EXISTS eventhub; USE eventhub;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),email VARCHAR(120) UNIQUE,password VARCHAR(255),role ENUM('student','organizer','admin') DEFAULT 'student');
CREATE TABLE events(id INT AUTO_INCREMENT PRIMARY KEY,organizer_id INT,title VARCHAR(120),description TEXT,venue VARCHAR(120),event_date DATETIME,slots INT,poster VARCHAR(80),FOREIGN KEY(organizer_id) REFERENCES users(id));
CREATE TABLE registrations(id INT AUTO_INCREMENT PRIMARY KEY,event_id INT,user_id INT,attended TINYINT DEFAULT 0,UNIQUE KEY one_per_event(event_id,user_id),FOREIGN KEY(event_id) REFERENCES events(id),FOREIGN KEY(user_id) REFERENCES users(id));
