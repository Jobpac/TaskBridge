-- Create the database
CREATE DATABASE quick_jobs;

-- Use the database
USE quick_jobs;

-- Create the workers table
CREATE TABLE workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL UNIQUE,
    photo VARCHAR(255) DEFAULT 'images/profile-photos/default.png',
    constituency VARCHAR(100) NOT NULL,
    ward VARCHAR(100) NOT NULL,
    county VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert new categories into the categories table
INSERT INTO categories (name) VALUES ('Mechanic'), ('Electrician'), ('Plumber'), ('Carpenter'), ('Painter'), ('mama fua'), ('cleaner');

-- Create the ratings table
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE
);