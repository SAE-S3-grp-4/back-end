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

        let h3 = document.createElement("h3");
        h3.innerText = month.charAt(0).toUpperCase() + month.slice(1); // Capitalize month name

        let eventList = document.createElement("div");
        eventList.className = "event-list";

        // Add events to the month container
        eventsByMonth[month].forEach((contents) => {
            let d = document.createElement("div");
            d.className = 'event';

            let span = document.createElement("span");
            span.innerText = contents.Date_Event;

            let p = document.createElement("p");
            p.innerText = contents.Nom_Event;

            let button = document.createElement("button");
            button.innerText = contents.Is_Registered ? "Désinscrire" : "S'inscrire";
            button.className = contents.Is_Registered ? "unsubscribe" : "subscribe";
            button.dataset.id = contents.Id_Event; // Set the data-id attribute for the button

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

ajaxRequest("GET", "php/controllerGestionEvent.php/events", loadEvents);