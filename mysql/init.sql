CREATE DATABASE IF NOT EXISTS watchdb;

USE watchdb;

CREATE TABLE IF NOT EXISTS watch (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100),
    model VARCHAR(100),
    price VARCHAR(50),
    currency VARCHAR(25),
    stores VARCHAR(25),
    description TEXT,
    dimensions VARCHAR(100),
    image_url TEXT,
    source TEXT
);
