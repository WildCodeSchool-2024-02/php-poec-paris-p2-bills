<?php

namespace App\Controller;

use App\Model\UserManager;

class LoginController extends AbstractController
{
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
    }

    /**
     * Display login page
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

            // S'il n'y a pas d'erreurs, effectuer la vérification avec les informations en BDD
            if (empty($errors)) {
                // Récupération des informations grâce à l'email
                $user = $this->userManager->getUserByEmail($userData['user_email']);

                if (is_array($user)) {
                    if ($user && password_verify($userData['user_password'], $user['password'])) {
                        // Authentification réussie, démarrage d'une session
                        $this->startSession();
                        $this->setSession('user_id', $user['id']);

                        header('Location: /dashboard');
                        exit();
                    } else {
                        $errors[] = 'Mot de passe incorrect, veuillez réessayer';
                    }
                } else {
                    $errors[] = 'Email incorrect, veuillez réessayer';
                }
            }
        }
        return $this->twig->render('Login/login.html.twig', ['errors' => $errors]);
    }

    public function logout(): void
    {
        // Déconnexion de l'utilisateur
        $this->destroySession();

        // Redirection vers la page d'accueil
        header('Location: /');
        exit();
    }
}
