function displayValidationMessage(message) {
    let validationMessageDiv = document.getElementById('validation-message');
    validationMessageDiv.innerText = message;
    validationMessageDiv.style.display = 'block';
    setTimeout(() => {
        validationMessageDiv.style.display = 'none';
    }, 3000); // Hide after 3 seconds
}

function displayErrorMessage(message) {
    let errorMessageDiv = document.getElementById('error-message');
    errorMessageDiv.innerText = message;
    errorMessageDiv.style.display = 'block';
    setTimeout(() => {
        errorMessageDiv.style.display = 'none';
    }, 3000); // Hide after 3 seconds
}

document.getElementById("retour-button").addEventListener('click', (event) => {
    window.location.href = "gestionBoutique.html";
});

document.getElementById('promo-form').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    ajaxRequest('POST', 'php/controllerGestionPromo.php/promo', function(response) {
        if (response.success) {
            displayValidationMessage('Code promo ajouté avec succès !');
            ajaxRequest('GET', 'php/controllerGestionPromo.php/promos', loadPromos); // Refresh the promo list
        } else {
            displayErrorMessage(response.error);
        }

        document.getElementById('promo-name').value = '';
        document.getElementById('promo-percentage').value = '';
        
    }, formData);
});

document.getElementById("promo-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-delete')) {
        const promoId = parseInt(event.target.dataset.id, 10);
        Swal.fire({
            title: "Etes-vous sûr ?",
            text: "Vous ne pourrez pas annuler cette action !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#00b4d8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                content: 'custom-swal-content',
                confirmButton: 'custom-swal-confirm-button',
                cancelButton: 'custom-swal-cancel-button'
            }
        }).then((result) => {
            if(result.isConfirmed){                
                ajaxRequest('DELETE', `php/controllerGestionPromo.php/promo/${promoId}`, (response) => {                    
                    const isSuccess = response === true || (response && response.success);
                    if(isSuccess){
                        Swal.fire({
                            title: 'Supprimé !',
                            text: 'Le code promo a été supprimé.',
                            icon: 'success',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                content: 'custom-swal-content',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        }).then(() => {
                            ajaxRequest('GET', 'php/controllerGestionPromo.php/promos', loadPromos);
                        });
                    } else {
                        const errorMsg = response?.error || 'Erreur inconnue';
                        console.error('Delete failed:', errorMsg);
                        Swal.fire({
                            title: 'Erreur !',
                            text: `Échec de la suppression du produit : ${errorMsg}`,
                            icon: 'error',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                content: 'custom-swal-content',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        });
                    }
                });
            }
        });
    }

    if (event.target.classList.contains('btn-modify')) {
        const promoId = parseInt(event.target.dataset.id, 10);
        if (!isNaN(promoId)) {
            ajaxRequest('GET', `php/controllerGestionPromo.php/promo/${promoId}`, (promo) => {
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

    console.log("Modification du code promo", id, nom, pourcentage);
    console.log("Données du formulaire:", formData);

    displayValidationMessage('Code promo modifié avec succès !');

    // Envoyer la requête
    ajaxRequest('POST', 'php/controllerGestionPromo.php/promo-modify', (response) => {
        console.log('Réponse du serveur:', response);
        ajaxRequest('GET', 'php/controllerGestionPromo.php/promos', loadPromos); // Rafraîchit la liste des promos
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

ajaxRequest("GET", "php/controllerGestionPromo.php/promos", loadPromos);