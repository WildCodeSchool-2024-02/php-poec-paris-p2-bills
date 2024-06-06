<?php

namespace App\Model;

use PDO;

class InvoiceManager extends AbstractManager
{
    public const TABLE2 = 'invoice';
    public const TABLE3 = 'product';

    /**
     * Insert invoice in database
     */
    public function insert(array $invoice): void
    {
        // Insérer les données dans la table 'invoice'
        $invoiceQuery = "INSERT INTO " . self::TABLE2 . "
                 (total_amount, created_at, due_at, user_siret, user_name, user_address, user_bank_details,
                 client_siret, client_name, client_address, user_id)
                 VALUES
                 (:total_amount, :created_at, :due_at, :user_siret, :user_name, :user_address, :user_bank_details,
                 :client_siret, :client_name, :client_address, :user_id)";

        $invoiceStatement = $this->pdo->prepare($invoiceQuery);

        $invoiceStatement->bindValue(':total_amount', $invoice['total_amount'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':created_at', $invoice['created_at'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':due_at', $invoice['due_at'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':user_siret', $invoice['user_siret'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':user_name', $invoice['user_name'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':user_address', $invoice['user_address'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':user_bank_details', $invoice['user_bank_details'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':client_siret', $invoice['client_siret'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':client_name', $invoice['client_name'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':client_address', $invoice['client_address'], PDO::PARAM_STR);
        $invoiceStatement->bindValue(':user_id', 1, PDO::PARAM_INT); // CODER EN DURE POUR LE MOMENT CHANGER PLUS TARD

        $invoiceStatement->execute();

        // Récupérer l'ID de la facture nouvellement insérée
        $invoiceId = $this->pdo->lastInsertId();

        $products = [];

        // On boucle sur le tableau invoice
        foreach ($invoice as $key => $value) {
            // On verifie les clés commencant par "product_name_"
            if (strpos($key, 'product_name_') === 0) {
                // Extraire le numéro du produit
                $productNumber = substr($key, strlen('product_name_'));

                // Récupérer les valeurs des autres attributs du produit
                $name = $value;
                $price = $invoice['product_price_' . $productNumber];
                $quantity = $invoice['product_quantity_' . $productNumber];

                // Ajouter les données du produit au tableau
                $products[] = array(
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                );
            }
        }

        // Préparer la requête d'insertion
        $productQuery = "INSERT INTO " . self::TABLE3 . " (name, quantity, price, invoice_id)
        VALUES (:name, :quantity, :price, :invoice_id)";

        $productStatement = $this->pdo->prepare($productQuery);

        // Insérer les données des produits dans la base de données
        foreach ($products as $product) {
            $productStatement->bindParam(':name', $product['name']);
            $productStatement->bindParam(':quantity', $product['quantity']);
            $productStatement->bindParam(':price', $product['price']);
            $productStatement->bindParam(':invoice_id', $invoiceId);
            $productStatement->execute();
        }
    }
}
