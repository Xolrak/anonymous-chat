CREATE DATABASE IF NOT EXISTS complaints_chat
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE complaints_chat;

-- Passwords table
CREATE TABLE IF NOT EXISTS Passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    passwd VARCHAR(255) NOT NULL
);

-- Users table
CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    id_passwd INT NOT NULL,
    is_admin TINYINT(1) DEFAULT 0, -- Default is_admin = 0 (False)
    FOREIGN KEY (id_passwd) REFERENCES Passwords(id)
);
