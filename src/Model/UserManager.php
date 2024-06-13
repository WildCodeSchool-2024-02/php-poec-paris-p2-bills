<?php

namespace App\Model;

use PDO;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    /**
     * Insert user in database
     */
    public function insert(array $userData,): void
    {
        $userQuery = "INSERT INTO " . self::TABLE . " (email, password) VALUES (:email, :password)";
        $userStatement = $this->pdo->prepare($userQuery);
        $userStatement->bindValue(':email', $userData['user_email'], PDO::PARAM_STR);
        $userStatement->bindValue(':password', $userData['user_password'], PDO::PARAM_STR);
        $userStatement->execute();
    }

     /**
     * Get user
     */
    public function getUserByEmail(string $userEmail): array | string
    {
        $userQuery = "SELECT * FROM " . self::TABLE . " WHERE email = :email";
        $userStatement = $this->pdo->prepare($userQuery);
        $userStatement->execute(['email' => $userEmail]);

        return $userStatement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete user in database
     */
    public function delete(int $id) : void
    {
        $userQuery = "DELETE FROM user WHERE id = $id";
        $userStatement = $this->pdo->query($userQuery);
        $userStatement->execute();
    }
}
