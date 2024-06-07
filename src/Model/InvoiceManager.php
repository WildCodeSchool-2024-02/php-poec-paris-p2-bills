<?php

namespace App\Model;

use PDO;

class InvoiceManager extends AbstractManager
{
    public const TABLE = 'invoice';

    /**
     * Insert invoice in database
     */
    public function insert(array $invoice): string
    {
        // Insérer les données dans la table 'invoice'
        $invoiceQuery = "INSERT INTO " . self::TABLE . "
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
        $invoiceStatement->bindValue(':user_id', 1, PDO::PARAM_INT);

        $invoiceStatement->execute();

        // Récupérer et retourne l'ID de la facture nouvellement insérée
        return $this->pdo->lastInsertId();
    }
}
