# Price-Comparator

## 📋 Objective
The goal of this project is to develop a price comparator capable of extracting product information from multiple e-commerce websites, storing the data in a database, and displaying it in a web interface. The interface will allow users to compare prices and access the product pages directly via clickable links.

## 📜 Description
Key Features:
Data scraping with Scrapy:
Extract product information (name, price, link, etc.) from 2–3 websites selling similar products.

Store data in a MySQL database:
The database will run inside a Docker container.

Web interface to visualize the data:
A web server will display the products, their prices, and highlight the website offering the lowest price.
Products will be clickable, redirecting users to the product's page.

Docker-based architecture:
A container for Scrapy.
A container for MySQL.
A container for Apache php
Docker Compose to orchestrate containers:
All services can be started using a single docker-compose.yml file.

## 🛠️ Technologies Used
Scrapy: For web scraping.
MySQL: To store product information.
Docker: To containerize all components.
Docker Compose: To manage and orchestrate containers.
php/HTML/CSS: For the user interface.
