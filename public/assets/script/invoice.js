document.addEventListener('DOMContentLoaded', () => {
    const addItemButton = document.getElementById('add-item');
    const productContainer = document.getElementById('product-container');
    let productCount = document.querySelectorAll('.product-line').length;

    // Fonction pour ajouter une nouvelle ligne de produit/service
    const addProductLine = () => {
        const template = document.querySelector('.product-line');
        const clone = template.cloneNode(true);

        productCount++;

        // Mettre à jour les names des inputs dans la nouvelle ligne clonée
        clone.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/_\d+$/, `_${productCount}`);
                input.setAttribute('name', newName);
                input.value = ''; // Réinitialiser la valeur de l'input
            }
        });

        // Ajouter un écouteur d'événements pour le bouton supprimer dans la nouvelle ligne
        clone.querySelector('.remove-item').addEventListener('click', function() {
            if (productContainer.querySelectorAll('.product-line').length > 1) {
                this.parentNode.remove();
                updateTotalAmount();
            }
        });

        // Ajouter des écouteurs d'événements pour les champs de prix et de quantité
        clone.querySelector('input[name^="product_price_"]').addEventListener('input', calculateLineTotal);
        clone.querySelector('input[name^="product_quantity_"]').addEventListener('input', calculateLineTotal);

        productContainer.appendChild(clone);
        updateTotalAmount();
    };

    // Écouteur pour le bouton Ajouter Article
    if (addItemButton) {
        addItemButton.addEventListener('click', addProductLine);
    }

    // Fonction pour calculer le total de chaque ligne
    function calculateLineTotal() {
        const line = this.closest('.product-line');
        const price = parseFloat(line.querySelector('input[name^="product_price_"]').value) || 0;
        const quantity = parseFloat(line.querySelector('input[name^="product_quantity_"]').value) || 0;
        const total = price * quantity;

        line.querySelector('input[name^="product_total_"]').value = total.toFixed(2);
        updateTotalAmount();
    }

    // Fonction pour mettre à jour le montant total
    function updateTotalAmount() {
        let total = 0;
        productContainer.querySelectorAll('.product-line').forEach(line => {
            const totalInput = line.querySelector('input[name^="product_total_"]');
            if (totalInput && !isNaN(parseFloat(totalInput.value))) {
                total += parseFloat(totalInput.value);
            }
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    // Initialiser les écouteurs d'événements pour les champs de prix et de quantité existants
    document.querySelectorAll('.product-line').forEach(line => {
        line.querySelector('input[name^="product_price_"]').addEventListener('input', calculateLineTotal);
        line.querySelector('input[name^="product_quantity_"]').addEventListener('input', calculateLineTotal);
        calculateLineTotal.call(line.querySelector('input[name^="product_price_"]')); // Calculer le total initialement
    });

    // Initialiser les boutons de suppression existants
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (productContainer.querySelectorAll('.product-line').length > 1) {
                this.parentNode.remove();
                updateTotalAmount();
            }
        });
    });
});

function confirmCancel(url) {
    if (confirm('Êtes-vous sûr de vouloir quitter cette page ?')) {
        window.location.href = url;
    }
}
