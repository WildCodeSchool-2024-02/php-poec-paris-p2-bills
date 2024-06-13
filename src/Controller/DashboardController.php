<?php

namespace App\Controller;

use App\Model\InvoiceManager;

class DashboardController extends AbstractController
{
    private InvoiceManager $invoiceManager;

    public function __construct()
    {
        parent::__construct();
        $this->invoiceManager = new InvoiceManager();
    }

    /**
     * Display dashboard
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

        return $this->twig->render('Dashboard/dashboard.html.twig', [
            'invoices' => $this->invoiceManager->getLastInvoices($_SESSION['user_id']),
            'totalMonth' => $this->invoiceManager->getTotalMonth($_SESSION['user_id']),
            'totalYear' => $this->invoiceManager->getTotalYear($_SESSION['user_id']),
        ]);
    }
}
