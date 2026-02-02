-- create database
CREATE DATABASE real_estate_db;
USE real_estate_db;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('super_admin', 'agent') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT 'default_user.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Properties Table
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('Apartment', 'House', 'Land', 'Commercial') NOT NULL,
    status ENUM('available', 'booked', 'sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Property Images Table
CREATE TABLE property_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Enquiries Table
CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    agent_id INT NOT NULL,
    viewer_name VARCHAR(100) NOT NULL,
    viewer_email VARCHAR(100) NOT NULL,
    viewer_phone VARCHAR(20),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Initial Super Admin
-- Password: admin123
INSERT INTO users (role, full_name, email, phone, password) 
VALUES ('super_admin', 'Super Admin', 'admin@admin.com', '1234567890', '$2y$10$gIGt89VOswFaqzO15iwet5NABCXZ2WWwC');
