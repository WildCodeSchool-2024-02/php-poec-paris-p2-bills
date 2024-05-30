-- Création de la table USER
CREATE TABLE `user` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `siret` CHAR(14) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `bank_details` VARCHAR(255),
    PRIMARY KEY (`id`)
);

-- Création de la table Invoices
CREATE TABLE `invoice` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `bill_status` VARCHAR(255),
    `due_at` DATE NOT NULL,
    `client_siret` CHAR(14),
    `client_name` VARCHAR(255),
    `client_address` VARCHAR(255),
    `created_at` DATE NOT NULL,
    `user_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
);

-- Création de la table Product
CREATE TABLE `product` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `invoice_id` INT NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`invoice_id`) REFERENCES `invoice`(`id`)
);