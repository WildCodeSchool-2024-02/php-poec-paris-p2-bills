<?php

namespace App\Controller;

use App\Model\InvoiceManager;
use App\Model\ProductManager;

class InvoiceController extends AbstractController
{
    private InvoiceManager $invoiceManager;
    private ProductManager $productManager;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceManager = new InvoiceManager();
        $this->productManager = new ProductManager();
    }

    /**
     * Display create invoice page
     */
    public function create(): string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice = array_map('htmlentities', array_map('trim', $_POST));

            if (
                empty($invoice['user_siret']) || !preg_match(
                    '/^[0-9]{14}$/',
                    $invoice['user_siret']
                )
            ) {
                $errors[] = 'Le numéro de Siret doit être renseigné et comporter 14 chiffres.';
            }
            if (
                empty($invoice['user_name']) || !preg_match(
                    '/^[A-Za-zÀ-ÿ \'-]+$/',
                    $invoice['user_name']
                )
            ) {
                $errors[] = 'Le nom doit être renseigné et ne contenir que des lettres, des espaces et des tirets.';
            }
            if (
                empty($invoice['user_address']) || !preg_match(
                    '/^[A-Za-zÀ-ÿ0-9 \'.,-]+$/',
                    $invoice['user_address']
                )
            ) {
                $errors[] = 'L\'adresse postale doit être renseignée et contenir des caractères valides.';
            }
            if (
                !empty($invoice['user_bank_details']) && !preg_match(
                    '/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/',
                    $invoice['user_bank_details']
                )
            ) {
                $errors[] = 'Veuillez entrer un IBAN valide.';
            }
            if (
                empty($invoice['total_amount']) || !preg_match(
                    '/^[0-9]+([.,][0-9]{1,2})?$/',
                    $invoice['total_amount']
                )
            ) {
                $errors[] = 'Veuillez entrer un montant valide';
            }

            // Si pas d'erreurs, insérer la facture puis redirection vers le dashboard
            if (empty($errors)) {
                // Insertion de la facture et récupération de l'ID
                $invoiceId = $this->invoiceManager->insert($invoice);

                // Insertion des articles associés
                $this->productManager->insert($invoice, $invoiceId);

                // Redirection vers le dashboard
                header('Location: /dashboard');
                exit;
            }
        }

        return $this->twig->render('Invoice/create_invoice.html.twig', [
            'errors' => $errors,
        ]);
    }
}
