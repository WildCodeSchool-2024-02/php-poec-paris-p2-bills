<?php

namespace App\Model;

use PDO;

class InvoiceManager extends AbstractManager
{
    public const TABLE = 'invoice';

    /**
     * Insert invoice in database
     */
    public function insert(array $invoice, int $userId): int
    {
        // Insérer les données dans la table 'invoice'
        $invoiceQuery = "INSERT INTO " . self::TABLE . "
                 (total_amount, created_at, due_at, user_siret, user_name, user_address, user_bank_details,
                 client_siret, client_name, client_address, user_id)
                 VALUES
                 (:total_amount, :created_at, :due_at, :user_siret, :user_name, :user_address, :user_bank_details,
                 :client_siret, :client_name, :client_address, $userId)";

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

        $invoiceStatement->execute();

        // Récupérer l'ID de la facture nouvellement insérée
        return $this->pdo->lastInsertId();
    }

    /**
     * Update invoice
     */
    public function update(array $invoice, int $id): void
    {
        // Mettre à jour les données dans la table 'invoice'
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

    /**
     * Display all invoices
     */
    public function getAllInvoices(int $userId): array
    {
        $query = "SELECT created_at, due_at, client_name, total_amount, id
                FROM " . self::TABLE . " WHERE user_id = $userId ORDER BY id DESC";
        $statement = $this->pdo->query($query);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Display last invoices
     */
    public function getLastInvoices(int $userId): array
    {
        $query = "SELECT created_at, due_at, client_name, total_amount, id
                FROM " . self::TABLE . " WHERE user_id = $userId ORDER BY id DESC LIMIT 8";
        $statement = $this->pdo->query($query);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculate total amount of the month
     */
    public function getTotalMonth(int $userId): int
    {
        $query = "SELECT SUM(total_amount) FROM invoice
        WHERE MONTH(created_at) = MONTH(CURDATE())
        AND YEAR(created_at) = YEAR(CURDATE())
        AND user_id = $userId";
        $statement = $this->pdo->query($query);
        $total = $statement->fetchColumn();
        return (int)$total;
    }

    /**
     * Calculate total amount of the year
     */
    public function getTotalYear(int $userId): int
    {
        $query = "SELECT SUM(total_amount) FROM " . self::TABLE . "
                WHERE YEAR(created_at) = YEAR(CURDATE())
                AND user_id = $userId";
        $statement = $this->pdo->query($query);
        $total = $statement->fetchColumn();
        return (int)$total;
    }

    /**
     * Select an invoice
     */
    public function selectOneById(int $id): array
    {
        $querySelectInvoiceData = "SELECT * FROM " . self::TABLE . " WHERE id = $id";
        $statementSelectInvoiceData = $this->pdo->query($querySelectInvoiceData);
        $invoice['infos'] = $statementSelectInvoiceData->fetch(PDO::FETCH_ASSOC);

        $querySelectProductData = "SELECT * FROM product WHERE invoice_id = $id";
        $statementSelectProductData = $this->pdo->query($querySelectProductData);
        $invoice['products'] = $statementSelectProductData->fetchAll(PDO::FETCH_ASSOC);

        return $invoice;
    }

    /**
     * Delete select invoice
     */
    public function deleteInvoice(int $id): void
    {
        $query = "DELETE FROM " . self::TABLE . " WHERE id = $id";
        $statement = $this->pdo->query($query);
        $statement->execute();
    }
}
