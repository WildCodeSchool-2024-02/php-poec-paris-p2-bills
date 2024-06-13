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

            if (empty($invoice['created_at'])) {
                $errors[] = 'La date de création doit être renseignée.';
            }

            if (empty($invoice['due_at'])) {
                $errors[] = 'La date d\'échéance doit être renseignée.';
            }

            if (date_create($invoice['due_at']) < date_create($invoice['created_at'])) {
                $errors[] = 'La date d\'échéance doit être supérieure à la date de création.';
            }

            if (empty($invoice['user_siret']) || !preg_match('/^[0-9]{14}$/', $invoice['user_siret'])) {
                $errors[] = 'Le numéro de Siret doit être renseigné et comporter 14 chiffres.';
            }

            if (empty($invoice['user_name']) || !preg_match('/^[A-Za-zÀ-ÿ \'-]+$/', $invoice['user_name'])) {
                $errors[] = 'Le nom doit être renseigné et ne contenir que des lettres, des espaces et des tirets.';
            }

            if (empty($invoice['user_address']) || !preg_match('/^[A-Za-zÀ-ÿ0-9 \'.,-]+$/', $invoice['user_address'])) {
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
                $errors[] = 'Veuillez entrer un montant valide.';
            }

            // Vérification des produits
            foreach ($invoice as $key => $value) {
                // On vérifie les clés commençant par "product_name_"
                if (strpos($key, 'product_name_') === 0) {
                    // Extraire le numéro du produit
                    $productNumber = substr($key, strlen('product_name_'));

                    // Récupérer les valeurs des autres attributs du produit
                    $productName = $value;
                    $productPrice = $invoice['product_price_' . $productNumber];
                    $productQuantity = $invoice['product_quantity_' . $productNumber];

                    // Vérification du nom du produit
                    if (empty($productName)) {
                        $errors[] = 'Le nom du produit ' . $productNumber . ' est requis.';
                    }
                    // Vérification du prix du produit
                    if (!is_numeric($productPrice) || $productPrice <= 0) {
                        $errors[] = 'Le prix du produit ' . $productNumber . ' doit être un nombre positif.';
                    }
                    // Vérification de la quantité du produit
                    if (!is_numeric($productQuantity) || $productQuantity <= 0) {
                        $errors[] = 'La quantité du produit ' . $productNumber . ' doit être un nombre positif.';
                    }
                }
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

    /**
     * Display edit invoice page
     */
    public function edit(int $id): string
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice = array_map('htmlentities', array_map('trim', $_POST));

            if (empty($invoice['created_at'])) {
                $errors[] = 'La date de création doit être renseignée.';
            }

            if (empty($invoice['due_at'])) {
                $errors[] = 'La date d\'échéance doit être renseignée.';
            }

            if (date_create($invoice['due_at']) < date_create($invoice['created_at'])) {
                $errors[] = 'La date d\'échéance doit être supérieure à la date de création.';
            }

            if (empty($invoice['user_siret']) || !preg_match('/^[0-9]{14}$/', $invoice['user_siret'])) {
                $errors[] = 'Le numéro de Siret doit être renseigné et comporter 14 chiffres.';
            }

            if (empty($invoice['user_name']) || !preg_match('/^[A-Za-zÀ-ÿ \'-]+$/', $invoice['user_name'])) {
                $errors[] = 'Le nom doit être renseigné et ne contenir que des lettres, des espaces et des tirets.';
            }

            if (empty($invoice['user_address']) || !preg_match('/^[A-Za-zÀ-ÿ0-9 \'.,-]+$/', $invoice['user_address'])) {
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
                $errors[] = 'Veuillez entrer un montant valide.';
            }

            if (empty($errors)) {
                $this->invoiceManager->update($invoice, $id);

                $this->productManager->deleteAllProducts($id);

                $this->productManager->insert($invoice, $id);

                header('Location: /invoices');
                exit;
            }
        }

        $invoice = $this->invoiceManager->selectOneById($id);
        return $this->twig->render('Invoice/edit_invoice.html.twig', [
            'invoice' => $invoice,
            'errors' => $errors,
        ]);
    }
}
