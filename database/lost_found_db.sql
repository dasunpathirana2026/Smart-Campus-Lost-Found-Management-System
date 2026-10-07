CREATE DATABASE IF NOT EXISTS lost_found_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lost_found_db;

DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','admin') NOT NULL DEFAULT 'student',
    status ENUM('active','blocked') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('lost','found') NOT NULL,
    item_date DATE NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(200) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    image VARCHAR(255) NULL,
    status ENUM('open','returned') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Demo admin:
-- Email: admin@smartcampus.com
-- Password: Admin@123
INSERT INTO users (name,email,phone,password,role,status)
VALUES ('System Admin','admin@smartcampus.com','0771234567',
'$2y$12$pNoCU86xvPvez5/9VvPQGuQkgKZqgVW/6JNVxtPEPFjtEUxDUET1y',
'admin','active');
-- Note: For security, change the admin password after first login.
