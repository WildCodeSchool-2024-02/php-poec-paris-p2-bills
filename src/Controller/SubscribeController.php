<?php

namespace App\Controller;

use App\Model\UserManager;

class SubscribeController extends AbstractController
{
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
    }

    /**
     * Display subscribe page
     */
    public function index(): string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userData = array_map('htmlentities', array_map('trim', $_POST));

            // Validation de l'email avec filter_var
            if (empty($userData['user_email']) || !filter_var($userData['user_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email doit être renseigné et être au format valide.';
            }

            if (
                empty($userData['user_password']) || !preg_match(
                    '/^[A-Za-zÀ-ÿ0-9 \'.,!@#$%^&*()_-]+$/',
                    $userData['user_password']
                )
            ) {
                $errors[] = 'Le mot de passe doit être renseigné et contenir des caractères valides.';
            }

            // S'il n'y a pas d'erreurs, insertion des données
            if (empty($errors)) {
                // Hashage du mot de passe avant insertion
                $userData['user_password'] = password_hash($userData['user_password'], PASSWORD_DEFAULT);

                // Insertion des données utilisateur
                $this->userManager->insert($userData);
                header('Location: /login');
                exit;
            }
        }
        return $this->twig->render('Subscribe/subscribe.html.twig', ['errors' => $errors]);
    }
}
