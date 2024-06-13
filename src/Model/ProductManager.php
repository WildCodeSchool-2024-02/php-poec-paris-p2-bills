<?php

namespace App\Model;

use PDO;

class ProductManager extends AbstractManager
{
    public const TABLE = 'product';

    /**
     * Insert product in database
     */
    public function insert(array $invoice, int $invoiceId): void
    {
        $products = [];

        // On boucle sur le tableau invoice
        foreach ($invoice as $key => $value) {
            // On vérifie les clés commençant par "product_name_"
            if (strpos($key, 'product_name_') === 0) {
                // Extraire le numéro du produit
                $productNumber = substr($key, strlen('product_name_'));

                // Récupérer les valeurs des autres attributs du produit
                $name = $value;
                $price = $invoice['product_price_' . $productNumber];
                $quantity = $invoice['product_quantity_' . $productNumber];

                // Ajouter les données du produit au tableau
                $products[] = [
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                ];
            }
        }

        $productQuery = "INSERT INTO " . self::TABLE . " (name, quantity, price, invoice_id)
        VALUES (:name, :quantity, :price, :invoice_id)";

        $productStatement = $this->pdo->prepare($productQuery);

        foreach ($products as $product) {
            $productStatement->bindValue(':name', $product['name']);
            $productStatement->bindValue(':quantity', $product['quantity']);
            $productStatement->bindValue(':price', $product['price']);
            $productStatement->bindValue(':invoice_id', $invoiceId);
            $productStatement->execute();
        }
    }

    /**
     * Delete product in database
     */
    public function deleteAllProducts(int $id): void
    {
        $productQuery = "DELETE FROM product WHERE invoice_id = $id";
        $productStatement = $this->pdo->query($productQuery);
        $productStatement->execute();
    }
}
