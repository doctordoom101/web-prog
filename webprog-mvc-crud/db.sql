-- Database: webprog_crud
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS pemweb1_crud_mvc;
USE pemweb1_crud_mvc;

-- Table structure for users
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_email (email),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional)
INSERT INTO users (name, email, phone) VALUES
('John Doe', 'john.doe@example.com', '08123456789'),
('Jane Smith', 'jane.smith@example.com', '08234567890'),
('Bob Johnson', 'bob.johnson@example.com', '08345678901'),
('Alice Brown', 'alice.brown@example.com', '08456789012'),
('Charlie Wilson', 'charlie.wilson@example.com', '08567890123');