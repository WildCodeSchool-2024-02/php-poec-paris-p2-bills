const apiKey = 'dc8e04e6-6918-3187-b1a5-5299de6e90ff';

document.addEventListener('DOMContentLoaded', function() {
    const clientSiretInput = document.getElementById('client_siret');
    const clientNameInput = document.getElementById('client_name');
    const clientAddressInput = document.getElementById('client_address');

    clientSiretInput.addEventListener('blur', function() {
        const siret = clientSiretInput.value;
        if (siret.length === 14) {
            fetch(`https://api.insee.fr/entreprises/sirene/V3.11/siren/005520135/${siret}`, {
                method: 'GET',
                headers: {
                    'Authorization': apiKey,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.etablissement) {
                    clientNameInput.value = data.etablissement.uniteLegale.denominationUniteLegale;
                    clientAddressInput.value = `${data.etablissement.adresseEtablissement.numeroVoieEtablissement} ${data.etablissement.adresseEtablissement.typeVoieEtablissement} ${data.etablissement.adresseEtablissement.libelleVoieEtablissement}, ${data.etablissement.adresseEtablissement.codePostalEtablissement} ${data.etablissement.adresseEtablissement.libelleCommuneEtablissement}`;
                } else {
                    alert("Aucune entreprise trouvée pour ce numéro de SIRET.");
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert("Erreur lors de la récupération des informations. Veuillez réessayer.");
            });
        }
    });
});