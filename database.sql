-- Création de la table USER

CREATE TABLE `user` (
    `id_user` INT NOT NULL AUTO_INCREMENT,
    `user_firstname` VARCHAR(255) NOT NULL,
    `user_lastname` VARCHAR(255) NOT NULL,
    `user_siret` CHAR(14) NOT NULL,
    `user_address` VARCHAR(255) NOT NULL,
    `user_email` VARCHAR(255) NOT NULL,
    `user_password` VARCHAR(255) NOT NULL,
    `user_bank_details` VARCHAR(255),
    PRIMARY KEY (`id_user`)
);

-- Création de la table Invoices

CREATE TABLE `invoice` (
    `id_invoice` INT NOT NULL AUTO_INCREMENT,
    `id_user` INT NOT NULL,
    `created_at` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `bill_status` VARCHAR(255),
    `client_siret` CHAR(14),
    `client_name` VARCHAR(255),
    `client_address` VARCHAR(255),
    PRIMARY KEY (`id_invoice`),
    FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`)
);

-- Création de la table Product

CREATE TABLE `product` (
    `id_product` INT NOT NULL AUTO_INCREMENT,
    `id_invoice` INT NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_quantity` INT NOT NULL,
    `product_price` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id_product`),
    FOREIGN KEY (`id_invoice`) REFERENCES `invoice`(`id_invoice`)
);