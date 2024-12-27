function displayValidationMessage(message) {
    let validationMessageDiv = document.getElementById('validation-message');
    validationMessageDiv.innerText = message;
    validationMessageDiv.style.display = 'block';
    setTimeout(() => {
        validationMessageDiv.style.display = 'none';
    }, 3000); // Hide after 3 seconds
}

document.getElementById("add-spreadsheet").addEventListener('submit', (event) => {
    event.preventDefault();
    let nom = document.getElementById('spreadsheet-name').value;
    let file = document.getElementById('spreadsheet-file').files[0];

    let formData = new FormData();
    formData.append('nom', nom);
    formData.append('file', file);

    console.log("Import d'une nouvelle feuille de calcul");

    ajaxRequest('POST', 'php/controllerGestionCompta.php/spreadsheet/', () => {
        ajaxRequest("GET","php/controllerGestionCompta.php/spreadsheets", loadSpreadsheet);
    }, formData);

    displayValidationMessage("Feuille de calcul importée avec succès !");

    document.getElementById('spreadsheet-name').value = '';
    document.getElementById('spreadsheet-file').value = '';
});

document.getElementById("spreadsheet-list").addEventListener('click', (event) => {
    if(event.target.classList.contains("btn-delete")){
        const spreadsheetId = parseInt(event.target.getAttribute("data-id"), 10);
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
                ajaxRequest('DELETE', `php/controllerGestionCompta.php/spreadsheet/${spreadsheetId}`, (response) => {
                    const isSuccess = response === true || (response && response.success);
                    if(isSuccess){
                        Swal.fire({
                            title: 'Supprimé !',
                            text: 'Le produit a été supprimé.',
                            icon: 'success',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                content: 'custom-swal-content',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        }).then(() => {
                            ajaxRequest('GET', 'php/controllerGestionCompta.php/spreadsheets', loadSpreadsheet);
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
});

document.getElementById("spreadsheet-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-modify')) {
        const spreadsheetId = parseInt(event.target.getAttribute('data-id'), 10);
        if (!isNaN(spreadsheetId)) {
            ajaxRequest('GET', `php/controllerGestionCompta.php/spreadsheet/${spreadsheetId}`, (spreadsheet) => {
                document.getElementById('modify-spreadsheet-id').value = spreadsheet.Id_Feu_Calc;
                document.getElementById('modify-spreadsheet-name').value = spreadsheet.Nom_Feu_Calc;
                document.getElementById('modify-spreadsheet-file').value = '';
                document.getElementById('modify-spreadsheet').style.display = 'block';

                // Scroll to the modification form
                document.getElementById('modify-spreadsheet').scrollIntoView({ behavior: 'smooth' });
            });
        } else {
            console.error('Invalid spreadsheet ID');
        }
    }
});

document.getElementById("form-modify-spreadsheet").addEventListener('submit', (event) => {
    event.preventDefault();

    let id = parseInt(document.getElementById('modify-spreadsheet-id').value, 10);
    let nom = document.getElementById('modify-spreadsheet-name').value.trim();
    let file = document.getElementById('modify-spreadsheet-file').files[0];

    // Validation des types
    if (!id || !nom ) {
        alert("Veuillez vérifier les champs. Tous doivent être correctement remplis.");
        return;
    }

    // Préparation des données
    let formData = new FormData();
    formData.append('id', id);
    formData.append('nom', nom);
    formData.append('file', file);

    displayValidationMessage("Feuille de calcul modifiée avec succès !");

    // Envoyer la requête
    ajaxRequest('POST', 'php/controllerGestionCompta.php/spreadsheet-modify', (response) => {
        console.log('Réponse du serveur:', response);
        ajaxRequest('GET', 'php/controllerGestionCompta.php/spreadsheets', loadSpreadsheet); // Rafraîchit la liste des produits
        document.getElementById('modify-spreadsheet').style.display = 'none';
    }, formData);
});





function loadSpreadsheet(spreadsheets) {
    if (!Array.isArray(spreadsheets)) {
        console.error("Invalid spreadsheet data: expected an array");
        return;
    }
    let container = document.getElementById("spreadsheet-list");
    container.innerHTML = ''; // Clear existing spreadsheets

    spreadsheets.forEach((spreadsheet) => {
        let spreadsheetCard = document.createElement("div");
        spreadsheetCard.className = 'spreadsheet-card';

        let spreadsheetName = document.createElement("h3");
        spreadsheetName.innerText = spreadsheet.Nom_Feu_Calc || "Nom inconnu";

        let spreadsheetDetails = document.createElement("div");
        spreadsheetDetails.className = 'spreadsheet-details';

        let spreadsheetDate = document.createElement("span");
        spreadsheetDate.innerText = "Dernière modification : " + spreadsheet.Date_Feu_Calc || "Date inconnue";

        spreadsheetDetails.append(spreadsheetDate);

        let spreadsheetActions = document.createElement("div");
        spreadsheetActions.className = 'spreadsheet-actions';

        let deleteButton = document.createElement("button");
        deleteButton.innerText = 'Supprimer';
        deleteButton.className = 'btn-delete';
        deleteButton.setAttribute("data-id", spreadsheet.Id_Feu_Calc);

        let modifyButton = document.createElement("button");
        modifyButton.innerText = 'Modifier';
        modifyButton.className = 'btn-modify';
        modifyButton.setAttribute("data-id", spreadsheet.Id_Feu_Calc);

        let spreadsheetFile = document.createElement("a");
        spreadsheetFile.href = "file/spreadsheet/" + encodeURIComponent(spreadsheet.Fichier_Feu_Calc || "default.xslx");
        spreadsheetFile.download = spreadsheet.Nom_Feu_Calc || "Feuille de calcul";

        let downloadButton = document.createElement("button");
        downloadButton.innerText = 'Télécharger';
        downloadButton.className = 'btn-download';
        downloadButton.setAttribute("data-id", spreadsheet.Id_Feu_Calc);


        spreadsheetFile.append(downloadButton);

        spreadsheetActions.append(deleteButton);
        spreadsheetActions.append(modifyButton);
        spreadsheetActions.append(spreadsheetFile);

        spreadsheetCard.append(spreadsheetName);
        spreadsheetCard.append(spreadsheetDetails);
        spreadsheetCard.append(spreadsheetActions);

        container.append(spreadsheetCard);
    });
}

ajaxRequest("GET","php/controllerGestionCompta.php/spreadsheets", loadSpreadsheet);