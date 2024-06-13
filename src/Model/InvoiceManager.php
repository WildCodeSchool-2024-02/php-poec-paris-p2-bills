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

    public function selectOneById($id): array
    {
        $querySelectInvoiceData = "SELECT * FROM " . self::TABLE . " WHERE id = $id";
        $statementSelectInvoiceData = $this->pdo->query($querySelectInvoiceData);
        $invoice['infos'] = $statementSelectInvoiceData->fetchAll(PDO::FETCH_ASSOC);

        $querySelectProductData = "SELECT * FROM product WHERE invoice_id = $id";
        $statementSelectProductData = $this->pdo->query($querySelectProductData);
        $invoice['products'] = $statementSelectProductData->fetchAll(PDO::FETCH_ASSOC);

        return $invoice;
    }
}
