CREATE DATABASE youcode_brief_7 ;

USE youcode_brief_7 ;

create table incomes (
    id INT PRIMARY KEY AUTO_INCREMENT ,
    montant INT NOT NULL ,
    description VARCHAR(500) NULL ,
    created_at DATE NOT null
) ;

create table categorys (
    id INT PRIMARY KEY AUTO_INCREMENT ,
    title VARCHAR(255) NOT NULL 
);

create table expenses (
    id INT PRIMARY KEY AUTO_INCREMENT ,
    montant INT NOT NULL ,
    description VARCHAR(500) NULL ,
    created_at DATE NOT NULL ,
    category_id INT NULL,
    CONSTRAINT fk_category
        FOREIGN KEY (category_id) REFERENCES categorys(id)
);

INSERT INTO incomes (montant, description, created_at) VALUES
(100, 'testing ...', '2025-12-01'),
(200, 'testing ...', '2005-02-01'),
(350, 'testing ...', '2025-01-02'),
(11, 'testing ...', '2025-09-03');

SELECT * FROM incomes ;

INSERT INTO expenses (montant, description, created_at) VALUES
(100, 'testing ...', '2025-12-01'),
(200, 'testing ...', '2005-02-01'),
(350, 'testing ...', '2025-01-02'),
(11, 'testing ...', '2025-09-03');

SELECT * FROM expenses ;