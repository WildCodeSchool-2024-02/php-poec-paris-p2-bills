function confirmDelete(url) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
        window.location.href = url;
    }
}