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
    window.location.href = "adminPanel.html";
});

document.getElementById("add-event").addEventListener('submit', (event) => {
    event.preventDefault();
    let nom = document.getElementById('event-title').value;
    let description = document.getElementById('event-description').value;
    let prix = document.getElementById('event-price').value;
    let date = document.getElementById('event-date').value;
    let nbPlace = document.getElementById('event-nbPlace').value;
    let dateFinInscription = document.getElementById('event-dateFinInscription').value;

    

    let data = `nom=${encodeURIComponent(nom)}&description=${encodeURIComponent(description)}&prix=${encodeURIComponent(prix)}&date=${encodeURIComponent(date)}&nbPlace=${encodeURIComponent(nbPlace)}&dateFinInscription=${encodeURIComponent(dateFinInscription)}`;

    ajaxRequest('POST', 'php/controllerGestionEvent.php/event', () => {
        ajaxRequest('GET', 'php/controllerGestionEvent.php/events', loadEvents);
    }, data);

    displayValidationMessage('Evenement ajouté avec succès !');

    document.getElementById('event-title').value = '';
    document.getElementById('event-description').value = '';
    document.getElementById('event-price').value = '';
    document.getElementById('event-date').value = '';
    document.getElementById('event-nbPlace').value = '';
    document.getElementById('event-dateFinInscription').value = '';
});



document.getElementById("event-list").addEventListener('click', (event) => {
    if(event.target.classList.contains('btn-delete')){
        const eventId = parseInt(event.target.dataset.id, 10);
        if(!isNaN(eventId)){
            Swal.fire({
                title: 'Etes-vous sûr ?',
                text: 'Vous ne pourrez pas annuler cette action !',
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
                    console.log('Attempting to delete event:', eventId);
                    ajaxRequest('DELETE', `php/controllerGestionEvent.php/event/${eventId}`, (response) => {
                        const isSuccess = response === true || (response && response.success);
                        if(isSuccess){
                            Swal.fire({
                                title: 'Supprimé !',
                                text: 'L\'événement a été supprimé.',
                                icon: 'success',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    title: 'custom-swal-title',
                                    content: 'custom-swal-content',
                                    confirmButton: 'custom-swal-confirm-button',
                                    cancelButton: 'custom-swal-cancel-button'
                                }
                            }).then(() => {
                                ajaxRequest('GET', 'php/controllerGestionEvent.php/events', loadEvents);
                            })
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
    }
})

document.getElementById("event-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-modify')) {
        const eventId = parseInt(event.target.dataset.id, 10); // Use dataset.id
        if (!isNaN(eventId)) {
            ajaxRequest('GET', `php/controllerGestionEvent.php/event/${eventId}`, (eventData) => {
                document.getElementById('modify-event-id').value = eventData.Id_Event;
                document.getElementById('modify-event-title').value = eventData.Nom_Event;
                document.getElementById('modify-event-description').value = eventData.Description_Event;
                document.getElementById('modify-event-date').value = eventData.Date_Event;
                document.getElementById('modify-event-price').value = eventData.Prix_Event;
                document.getElementById('modify-event-nbPlace').value = eventData.Nb_Place_Event;
                document.getElementById('modify-event-dateFinInscription').value = eventData.Date_Fin_Inscription;
                document.getElementById('modify-event').style.display = 'block';

                // Scroll to the modification form
                document.getElementById('modify-event').scrollIntoView({ behavior: 'smooth' });
            });
        } else {
            console.error('Invalid event ID');
        }
    }
});

document.getElementById("form-modify-event").addEventListener('submit', (event) => {
    event.preventDefault();

    let id = document.getElementById('modify-event-id').value;
    let nom = document.getElementById('modify-event-title').value;
    let description = document.getElementById('modify-event-description').value;
    let date = document.getElementById('modify-event-date').value;
    let prix = document.getElementById('modify-event-price').value;
    let nbPlace = document.getElementById('modify-event-nbPlace').value;
    let dateFinInscription = document.getElementById('modify-event-dateFinInscription').value;

    let data = new FormData();
    data.append('id', id);
    data.append('nom', nom);
    data.append('description', description);
    data.append('date', date);
    data.append('prix', prix);
    data.append('nbPlace', nbPlace);
    data.append('dateFinInscription', dateFinInscription);

    displayValidationMessage('Evenement modifié avec succès !');

    ajaxRequest('POST', 'php/controllerGestionEvent.php/event-modify', (response) => {
        console.log('Réponse du serveur:', response);
        ajaxRequest('GET', 'php/controllerGestionEvent.php/events', loadEvents); // Rafraîchit la liste des événements
        document.getElementById('modify-event').style.display = 'none';
    }, data);
});

function loadEvents(events) {
    console.log("Loading events:", events);
    let container = document.getElementById("event-list");
    container.innerHTML = ''; // Clear existing events

    events.forEach((contents) => {
        let d = document.createElement("div");
        d.className = 'event-card';

        let h3 = document.createElement("h3");
        h3.innerText = contents.Nom_Event;

        let p = document.createElement("p");
        p.innerText = contents.Description_Event;

        d.append(h3);
        d.append(p);

        let details = document.createElement("div");
        details.className = 'event-details';

        let sp = document.createElement("span");
        sp.innerText = ' Prix: ' + contents.Prix_Event + '€ ';

        let sp2 = document.createElement("span");
        sp2.innerText = ' Date: ' + contents.Date_Event;

        let sp3 = document.createElement("span");
        sp3.innerText = '\n Places: ' + contents.Nb_Place_Event;

        let sp4 = document.createElement("span");
        sp4.innerText = ' Fin d\'inscription: ' + contents.Date_Fin_Inscription;

        details.append(sp);
        details.append(sp2);
        details.append(sp3);
        details.append(sp4);

        let actions = document.createElement("div");
        actions.className = 'event-actions';

        let btndel = document.createElement("button");
        btndel.innerText = 'Supprimer';
        btndel.className = 'btn-delete';
        btndel.dataset.id = contents.Id_Event; // Set the data-id attribute

        let btnmod = document.createElement("button");
        btnmod.innerText = 'Modifier';
        btnmod.className = 'btn-modify';
        btnmod.dataset.id = contents.Id_Event; // Set the data-id attribute

        actions.append(btndel);
        actions.append(btnmod);

        d.append(details);
        d.append(actions);

        container.append(d);
    });
}

ajaxRequest("GET", "php/controllerGestionEvent.php/events", loadEvents);

