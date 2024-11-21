document.getElementById("add-event").addEventListener('submit', (event) => {
    event.preventDefault();
    let nom = document.getElementById('event-title').value;
    let description = document.getElementById('event-description').value;
    let prix = document.getElementById('event-price').value;
    let date = document.getElementById('event-date').value;

    console.log("Ajout d'un event");

    let data = `nom=${encodeURIComponent(nom)}&description=${encodeURIComponent(description)}&prix=${encodeURIComponent(prix)}&date=${encodeURIComponent(date)}`;

    ajaxRequest('POST', 'php/request.php/event', () => {
        ajaxRequest('GET', 'php/request.php/events', loadEvents);
    }, data);

    document.getElementById('event-title').value = '';
    document.getElementById('event-description').value = '';
    document.getElementById('event-price').value = '';
    document.getElementById('event-date').value = '';
});

document.getElementById("event-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-delete')) {
        const eventId = parseInt(event.target.dataset.id, 10);
        if (!isNaN(eventId)) {
            ajaxRequest('DELETE', `php/request.php/event/${eventId}`, () => {
                ajaxRequest('GET', 'php/request.php/events', loadEvents);
            });
        } else {
            console.error('Invalid event ID');
        }
    }
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
        sp.innerText = contents.Prix_Event + '€ ';

        let sp2 = document.createElement("span");
        sp2.innerText = contents.Date_Event;

        details.append(sp);
        details.append(sp2);

        let actions = document.createElement("div");
        actions.className = 'event-actions';

        let btndel = document.createElement("button");
        btndel.innerText = 'Supprimer';
        btndel.className = 'btn-delete';
        btndel.dataset.id = contents.Id_Event; // Set the data-id attribute

        let btnmod = document.createElement("button");
        btnmod.innerText = 'Modifier';
        btnmod.className = 'btn-modify';

        actions.append(btndel);
        actions.append(btnmod);

        d.append(details);
        d.append(actions);

        container.append(d);
    });
}

ajaxRequest("GET", "php/request.php/events", loadEvents);