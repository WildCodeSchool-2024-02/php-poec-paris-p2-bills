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

        return $this->pdo->lastInsertId();
    }

    /**
     * Update static part of invoice in database
     */
    public function update(array $invoice, int $id): void
    {
        $invoiceQuery = "UPDATE " . self::TABLE . "
                        SET total_amount = :total_amount,
                            created_at = :created_at,
                            due_at = :due_at,
                            user_siret = :user_siret,
                            user_name = :user_name,
                            user_address = :user_address,
                            user_bank_details = :user_bank_details,
                            client_siret = :client_siret,
                            client_name = :client_name,
                            client_address = :client_address
                        WHERE id = :id";

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
        $invoiceStatement->bindValue(':id', $id, PDO::PARAM_INT);

        $invoiceStatement->execute();
    }

    public function getInvoiceById($id): array
    {
        $query1 = "SELECT * FROM " . self::TABLE . " WHERE id = $id";
        $statement1 = $this->pdo->query($query1);
        $invoice['infos'] = $statement1->fetchAll(PDO::FETCH_ASSOC);

        $query2 = "SELECT * FROM product WHERE invoice_id = $id";
        $statement2 = $this->pdo->query($query2);
        $invoice['products'] = $statement2->fetchAll(PDO::FETCH_ASSOC);

        return $invoice;
    }
}
