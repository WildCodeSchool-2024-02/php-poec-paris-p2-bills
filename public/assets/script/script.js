// Fonction pour calculer le montant total d'un produit en fonction du prix unitaire et de la quantité
function calculateProductTotal(itemCount) {
    const priceInput = document.querySelector(`input[name="product_price_${itemCount}"]`);
    const quantityInput = document.querySelector(`input[name="product_quantity_${itemCount}"]`);
    const totalInput = document.querySelector(`input[name="product_total_${itemCount}"]`);

    const price = parseFloat(priceInput.value.replace(',', '.'));
    const quantity = parseFloat(quantityInput.value.replace(',', '.'));

    const total = price * quantity;

    totalInput.value = total.toFixed(2);
}

// Fonction pour recalculer les montants totaux des produits
function recalculateProductTotals() {
    const productLines = document.getElementsByClassName('product-line');

    for (let i = 0; i < productLines.length; i++) {
        calculateProductTotal(i + 1);
    }

    calculateTotalAmount(); // Appel de la fonction de calcul du montant total global à chaque fois qu'un champ est modifié
}

// Fonction pour calculer le montant total global
function calculateTotalAmount() {
    let totalAmount = 0;
    const productTotalInputs = document.querySelectorAll('input[name^="product_total_"]');

    productTotalInputs.forEach(function(input) {
        totalAmount += parseFloat(input.value.replace(',', '.'));
    });

    const totalAmountInput = document.getElementById('total_amount');
    totalAmountInput.value = totalAmount.toFixed(2);
}

// Ajout des écouteurs d'événements pour recalculer les montants lors de la modification des champs
document.getElementById('product-container').addEventListener('input', function(event) {
    if (event.target.matches('input[name^="product_price_"]') || event.target.matches('input[name^="product_quantity_"]')) {
        recalculateProductTotals();
    }
});


// Fonction pour ajouter une nouvelle ligne de produit

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('product-container');
    const itemCount = container.getElementsByClassName('product-line').length + 1;
    const newLine = document.createElement('div');
    newLine.className = 'product-line';
    newLine.innerHTML = `
        <input type="text" name="product_name_${itemCount}" placeholder="Désignation du produit ou service" maxlength="100" pattern="[A-Za-zÀ-ÿ0-9 '-]" required>
        <input type="text" name="product_price_${itemCount}" placeholder="Prix unitaire" maxlength="10" pattern="[1-9]+([,\.][0-9]{1,2})?" required>
        <input type="text" name="product_quantity_${itemCount}" placeholder="Quantité" maxlength="5" pattern="[1-9]+([,\.][0-9]{1,2})?" required>
        <input type="text" name="product_total_${itemCount}" placeholder="Total" maxlength="10" pattern="[1-9]+([,\.][0-9]{1,2})?" required readonly>
        <button type="button" class="remove-item">Supprimer article</button>
    `;
    container.appendChild(newLine);
});

// Fonction pour supprimer une ligne de produit

document.getElementById('product-container').addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-item')) {
        event.target.parentElement.remove();
        calculateTotalAmount(); // Recalcul du montant total après la suppression d'une ligne de produit
    }
});
