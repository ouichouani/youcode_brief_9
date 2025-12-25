show databases;

CREATE DATABASE youcode_brief_9;

USE youcode_brief_9;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description text null,
    limits int not null,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT NULL,
    CONSTRAINT fk_userid_categories FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

create table incomes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount INT NOT NULL,
    description VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    category_id INT NULL,
    CONSTRAINT fk_categoryid_incomes FOREIGN KEY (category_id) REFERENCES categories (id)
);

create table expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount INT NOT NULL,
    description VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    category_id INT NULL,
    CONSTRAINT fk_categoryid_expenses FOREIGN KEY (category_id) REFERENCES categories (id)
);

SHOW TABLES;




SELECT * FROM users;

USE youcode_brief_9;

