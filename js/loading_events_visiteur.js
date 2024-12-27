document.addEventListener('DOMContentLoaded', () => {
    const agendaElement = document.getElementById("agenda");
    if (agendaElement) {
        agendaElement.addEventListener('click', (event) => {
            if (event.target.classList.contains('subscribe')) {
                ajaxRequest('GET', 'php/check_connexion.php', (response) => {
                    if (response.is_connected) {
                        const eventId = parseInt(event.target.getAttribute('data-id'), 10);
                        if (!isNaN(eventId)) {
                            ajaxRequest('GET', 'php/controllerEvent.php/isLogged', (response) => {
                                if (response) {
                                    console.log("User is logged in");
                                    ajaxRequest('GET', `php/controllerEvent.php/addToCart/${eventId}`, (response) => {
                                        console.log("Add to cart response", response);
                                        if (response) {
                                            event.target.innerText = "Désinscrire";
                                            event.target.classList.remove('subscribe');
                                            event.target.classList.add('unsubscribe');
                                            alert('Evenement ajouté au panier');
                                        } else {
                                            alert('Erreur lors de l\'ajout au panier');
                                        }
                                    });
                                } else {
                                    console.error('User is not logged in', response);
                                }
                            });
                        } else {
                            console.error('Invalid event ID');
                        }
                    } else {
                        alert("Vous devez être connecté pour ajouter un évenement au panier");
                    }
                });
            }
        });
        agendaElement.addEventListener('click', (event) => {
            if (event.target.classList.contains('unsubscribe')) {
                ajaxRequest('GET', 'php/check_connexion.php', (response) => {
                    if (response.is_connected) {
                        const eventId = parseInt(event.target.getAttribute('data-id'), 10);
                        if (!isNaN(eventId)) {
                            ajaxRequest('GET', 'php/controllerEvent.php/isLogged', (response) => {
                                if (response) {
                                    console.log("User is logged in");
                                    ajaxRequest('GET', `php/controllerEvent.php/delFromCart/${eventId}`, (response) => {
                                        console.log("Del from cart response", response);
                                        if (response) {
                                            event.target.innerText = "S'inscrire";
                                            event.target.classList.remove('unsubscribe');
                                            event.target.classList.add('subscribe');
                                            alert('Evenement supprimé du panier');
                                        } else {
                                            alert('Erreur lors de la suppression du panier');
                                        }
                                    });
                                } else {
                                    console.error('User is not logged in', response);
                                }
                            });
                        } else {
                            console.error('Invalid event ID');
                        }
                    } else {
                        alert("Vous devez être connecté pour ajouter un évenement au panier");
                    }
                });
            }
        });
    } else {
        console.error('Element with ID "agenda" not found');
    }
});

function loadEvents(events) {
    console.log("Loading events:", events);

    // Clear the agenda section
    let agendaSection = document.querySelector(".agenda");
    agendaSection.innerHTML = '';

    // Group events by month
    let eventsByMonth = {};
    events.forEach((event) => {
        let month = new Date(event.Date_Event).toLocaleString('fr-FR', { month: 'long' });
        if (!eventsByMonth[month]) {
            eventsByMonth[month] = [];
        }
        eventsByMonth[month].push(event);
    });

    // Populate events by month
    Object.keys(eventsByMonth).forEach((month) => {
        // Create month container
        let monthContainer = document.createElement("div");
        monthContainer.className = "month";
        monthContainer.id = "month";

        let h3 = document.createElement("h3");
        h3.innerText = month.charAt(0).toUpperCase() + month.slice(1); // Capitalize month name

        let eventList = document.createElement("div");
        eventList.className = "event-list";

        // Add events to the month container
        eventsByMonth[month].forEach((event) => {
            let d = document.createElement("div");
            d.className = 'event';

            let span = document.createElement("span");
            span.innerText = event.Date_Event;

            let p = document.createElement("p");
            p.innerText = event.Nom_Event;

            let button = document.createElement("button");
            button.setAttribute("data-id", event.Id_Event);
            ajaxRequest('GET', 'php/check_connexion.php', (response) => {
                if (response.is_connected) {
                    ajaxRequest('GET', `php/controllerEvent.php/isSubscribed/${event.Id_Event}`, (response) => {
                        //event.isSubscribed = response.isSubscribed;
                        event.isSubscribed = response;
                        button.innerText = event.isSubscribed ? "Désinscrire" : "S'inscrire";
                        button.className = event.isSubscribed ? "unsubscribe" : "subscribe";
                    });
                }else {
                    event.isSubscibed = false;
                    button.innerText = event.isSubscribed ? "Désinscrire" : "S'inscrire";
                    button.className = event.isSubscribed ? "unsubscribe" : "subscribe";
                }
            });



            d.append(span);
            d.append(p);
            d.append(button);

            eventList.append(d);
        });

        monthContainer.append(h3);
        monthContainer.append(eventList);
        agendaSection.append(monthContainer);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    ajaxRequest("GET", "php/controllerGestionEvent.php/events", loadEvents);
});