CREATE DATABASE IF NOT EXISTS watchdb;

USE watchdb;

CREATE TABLE IF NOT EXISTS watch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100),
    model VARCHAR(255),
    price VARCHAR(50),
    currency VARCHAR(100),
    stores VARCHAR(100),
    image_url VARCHAR(255),
    source TEXT
);
