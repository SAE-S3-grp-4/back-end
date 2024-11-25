

document.getElementById("promo-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-delete')) {
        const promoId = parseInt(event.target.dataset.id, 10);
        if (!isNaN(promoId)) {
            ajaxRequest('DELETE', `php/request.php/promo/${promoId}`, () => {
                ajaxRequest('GET', 'php/request.php/promos', loadPromos);
            });
        } else {
            console.error('Invalid promo ID');
        }
    }

    if (event.target.classList.contains('btn-modify')) {
        const promoId = parseInt(event.target.dataset.id, 10);
        if (!isNaN(promoId)) {
            ajaxRequest('GET', `php/request.php/promo/${promoId}`, (promo) => {
                document.getElementById('modify-promo-id').value = promo.Id_Promo;
                document.getElementById('modify-promo-name').value = promo.Nom_Promo;
                document.getElementById('modify-promo-percentage').value = promo.Pourcentage_Promo;
                document.getElementById('modify-promo').style.display = 'block';

                // Scroll to the modification form
                document.getElementById('modify-promo').scrollIntoView({ behavior: 'smooth' });
            });
        } else {
            console.error('Invalid promo ID');
        }
    }
});

document.getElementById("form-modify-promo").addEventListener('submit', (event) => {
    event.preventDefault();

    let id = parseInt(document.getElementById('modify-promo-id').value, 10);
    let nom = document.getElementById('modify-promo-name').value.trim();
    let pourcentage = parseFloat(document.getElementById('modify-promo-percentage').value);

    // Validation des types
    if (!id || !nom || isNaN(pourcentage)) {
        alert("Veuillez vérifier les champs. Tous doivent être correctement remplis.");
        return;
    }

    // Préparation des données
    let formData = new FormData();
    formData.append('id', id);
    formData.append('nom', nom);
    formData.append('pourcentage', pourcentage.toFixed(2)); // Pourcentage formaté avec 2 décimales

    console.log("Modification du promo", id, nom, pourcentage);
    console.log("Données du formulaire:", formData);

    // Envoyer la requête
    ajaxRequest('POST', 'php/request.php/promo-modify', (response) => {
        console.log('Réponse du serveur:', response);
        ajaxRequest('GET', 'php/request.php/promos', loadPromos); // Rafraîchit la liste des promos
        document.getElementById('modify-promo').style.display = 'none';
    }, formData);
});


function loadPromos(promos) {
    console.log('Promos:', promos); // Debugging line
    if (!Array.isArray(promos)) {
        console.error("Invalid promos data: expected an array");
        return;
    }
    let container = document.getElementById("promo-list");
    container.innerHTML = ''; // Clear existing promos

    promos.forEach((promo) => {
        let promoCard = document.createElement("div");
        promoCard.className = 'promo-card';

        let promoName = document.createElement("h3");
        promoName.innerText = promo.Nom_Promo || "Nom inconnu";

        let promoDetails = document.createElement("div");
        promoDetails.className = 'promo-details';

        let promoPercentage = document.createElement("span");
        promoPercentage.innerText = promo.Pourcentage_Promo + '%';

        promoDetails.append(promoPercentage);

        let promoActions = document.createElement("div");
        promoActions.className = 'promo-actions';

        let deleteButton = document.createElement("button");
        deleteButton.innerText = 'Supprimer';
        deleteButton.className = 'btn-delete';
        deleteButton.setAttribute("data-id", promo.Id_Promo);

        let modifyButton = document.createElement("button");
        modifyButton.innerText = 'Modifier';
        modifyButton.className = 'btn-modify';
        modifyButton.setAttribute("data-id", promo.Id_Promo);

        promoActions.append(deleteButton);
        promoActions.append(modifyButton);

        promoCard.append(promoName);
        promoCard.append(promoDetails);
        promoCard.append(promoActions);

        container.append(promoCard);
    });
}

ajaxRequest("GET", "php/request.php/promos", loadPromos);