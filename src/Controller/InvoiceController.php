<?php

namespace App\Controller;

use App\Model\InvoiceManager;

class InvoiceController extends AbstractController
{
    private InvoiceManager $invoiceManager;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceManager = new InvoiceManager();
    }

    /**
     * Display create invoice page
     */
    public function create(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice = array_map('htmlentities', array_map('trim', $_POST));

            $errors = [];

            if (
                empty($invoice['created_at']) ||
                empty($invoice['due_at']) ||
                empty($invoice['user_siret']) ||
                empty($invoice['user_name']) ||
                empty($invoice['user_address']) ||
                empty($invoice['total_amount'])
            ) {
                $errors[] = 'Les champs marqués d\'un * sont obligatoires.';
            }

            // Si pas d'erreurs, insérer la facture puis redirection vers le dashboard
            if (empty($errors)) {
                $this->invoiceManager->insert($invoice);
                header('Location: /dashboard');
                exit;
            }

            // Affichage des erreurs dans le template
            return $this->twig->render('Invoice/create_invoice.html.twig', [
                'errors' => $errors,
            ]);
        }

        // Affichage initial du formulaire
        return $this->twig->render('Invoice/create_invoice.html.twig');
    }
}
