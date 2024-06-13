<?php

namespace App\Controller;

use App\Model\InvoiceManager;
use App\Model\ProductManager;
use App\Model\SearchManager;

class InvoiceController extends AbstractController
{
    private InvoiceManager $invoiceManager;
    private ProductManager $productManager;
    private SearchManager $searchManager;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceManager = new InvoiceManager();
        $this->productManager = new ProductManager();
        $this->searchManager = new SearchManager();
    }

    /**
     * Display all invoices
     */
    public function index()
    {
        // Vérifier si l'utilisateur est connecté
        $this->startSession();
        if (!$this->getSession('user_id')) {
            // Rediriger vers la page de connexion si non connecté
            header('Location: /login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search-request'])) {
            $searchRequest = htmlentities(trim($_POST['search-request']));

            return $this->twig->render('Invoice/history_invoices.html.twig', [
                'invoices' => $this->searchManager->search($searchRequest, $_SESSION['user_id']),
            ]);
        }

        return $this->twig->render('Invoice/history_invoices.html.twig', [
            'invoices' => $this->invoiceManager->getAllInvoices($_SESSION['user_id']),
        ]);
    }

    /**
     * Display create invoice page
     */
    public function create(): string
    {
        // Vérifier si l'utilisateur est connecté
        $this->startSession();
        if (!$this->getSession('user_id')) {
            // Rediriger vers la page de connexion si non connecté
            header('Location: /login');
            exit();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice = array_map('htmlentities', array_map('trim', $_POST));

            if (
                empty($invoice['created_at'])
            ) {
                $errors[] = 'La date de création doit être renseignée.';
            }
            if (
                empty($invoice['due_at'])
            ) {
                $errors[] = 'La date d\'échéance doit être renseignée.';
            }
            if (
                date_create($invoice['due_at']) < date_create($invoice['created_at'])
            ) {
                $errors[] = 'La date d\'échéance doit être supérieure à la date de création.';
            }
            if (
                empty($invoice['user_siret']) || !preg_match(
                    '/^[0-9]{14}$/',
                    $invoice['user_siret']
                )
            ) {
                $errors[] = 'Le numéro de Siret doit être renseigné et comporter 14 chiffres.';
            }
            if (
                empty($invoice['user_name'])
            ) {
                $errors[] = 'Le nom doit être renseigné.';
            }
            if (
                empty($invoice['user_address'])
            ) {
                $errors[] = 'L\'adresse postale doit être renseignée.';
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
                // Insertion de la partie non dynamique de la facture et récupération de l'ID
                $invoiceId = $this->invoiceManager->insert($invoice, $_SESSION['user_id']);

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
        // Vérifier si l'utilisateur est connecté
        $this->startSession();
        if (!$this->getSession('user_id')) {
            // Rediriger vers la page de connexion si non connecté
            header('Location: /login');
            exit();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice = array_map('htmlentities', array_map('trim', $_POST));

            if (
                empty($invoice['created_at'])
            ) {
                $errors[] = 'Le date de création doit être renseignée.';
            }
            if (
                empty($invoice['due_at'])
            ) {
                $errors[] = 'Le date d\'échéance doit être renseignée.';
            }
            if (
                date_create($invoice['due_at']) < date_create($invoice['created_at'])
            ) {
                $errors[] = 'La date d\'échéance doit être supérieure à la date de création.';
            }
            if (
                empty($invoice['user_siret']) || !preg_match(
                    '/^[0-9]{14}$/',
                    $invoice['user_siret']
                )
            ) {
                $errors[] = 'Le numéro de Siret doit être renseigné et comporter 14 chiffres.';
            }
            if (
                empty($invoice['user_name'])
            ) {
                $errors[] = 'Le nom doit être renseigné.';
            }
            if (
                empty($invoice['user_address'])
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

            // Si pas d'erreurs, mettre a jour les informations de  la facture
            if (empty($errors)) {
                // MAJ de la facture
                $this->invoiceManager->update($invoice, $id);

                // Suppression des anciens articles
                $this->productManager->deleteAllProducts($id);

                // Insertion des articles associés
                $this->productManager->insert($invoice, $id);

                // Redirection vers l'historique des factures
                header('Location: /invoices');
                exit;
            }
        }

        // Affichage initial de la facture
        return $this->twig->render('Invoice/edit_invoice.html.twig', [
            'invoice' => $this->invoiceManager->selectOneById($id),
            'errors' => $errors,
        ]);
    }

    /**
     * Display a specific invoice
     */
    public function show(int $id): string
    {
        // Vérifier si l'utilisateur est connecté
        $this->startSession();
        if (!$this->getSession('user_id')) {
            // Rediriger vers la page de connexion si non connecté
            header('Location: /login');
            exit();
        }

        return $this->twig->render('Invoice/display_invoice.html.twig', [
            'invoice' => $this->invoiceManager->selectOneById($id),
        ]);
    }

    /**
     * Delete a specific invoice
     */
    public function delete($id): void
    {
        $this->invoiceManager->deleteInvoice($id);
        header('Location: /invoices');
        exit;
    }
}
