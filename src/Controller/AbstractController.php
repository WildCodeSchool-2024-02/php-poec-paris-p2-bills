<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Initialized some Controller common features (Twig, Sessions...)
 */
abstract class AbstractController
{
    protected Environment $twig;

    public function __construct()
    {
        // Initialiser le chargeur de fichiers pour Twig
        $loader = new FilesystemLoader(APP_VIEW_PATH);

        // Configurer l'environnement Twig
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => true,
            ]
        );

        // Ajouter l'extension de débogage Twig
        $this->twig->addExtension(new DebugExtension());

        // Initialiser la session
        $this->startSession();
    }

    /**
     * Démarre une session si elle n'est pas déjà démarrée.
     */
    protected function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Définit une variable de session.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setSession(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une variable de session.
     *
     * @param string $key
     * @return mixed|null
     */
    protected function getSession(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Supprime une variable de session.
     *
     * @param string $key
     */
    protected function removeSession(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Détruit la session.
     */
    protected function destroySession()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Render a template and pass common variables including session data.
     *
     * @param string $template
     * @param array $data
     * @return void
     */
    protected function render(string $template, array $data = [])
    {
        // Ajouter les données de session aux données passées à Twig
        $data['session'] = $_SESSION ?? [];

        // Rendre le template avec les données
        echo $this->twig->render($template, $data);
    }
}
