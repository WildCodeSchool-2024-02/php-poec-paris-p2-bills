<?php

namespace App\Model;

use PDO;

class SearchManager extends AbstractManager
{
    public const TABLE = 'invoice';

    public function search(string $searchRequest, int $userId): array
    {
        $searchRequest = "%" . $searchRequest . "%";
        $query = "SELECT created_at, due_at, client_name, total_amount, id
                FROM " . self::TABLE . "
                WHERE client_name LIKE :searchRequest
                AND user_id = $userId
                OR CAST(total_amount AS CHAR) LIKE :searchRequest
                OR CAST(created_at AS CHAR) LIKE :searchRequest
                ORDER BY created_at DESC, total_amount DESC";
        $searchStatement = $this->pdo->prepare($query);
        $searchStatement->bindValue(':searchRequest', $searchRequest, PDO::PARAM_STR);
        $searchStatement->execute();
        return $searchStatement->fetchAll(PDO::FETCH_ASSOC);
    }
}
