function loadAccueil(events) {
  console.log("Loading events:", events);
  let container = document.getElementsByClassName("events-list")[0];
  container.innerHTML = ""; // Clear existing events

  events.forEach((contents) => {
    let eventCard = document.createElement("div");
    eventCard.className = "event-card";

    let h4 = document.createElement("h4");
    h4.innerHTML = contents["Nom_Event"];

    let p = document.createElement("p");
    p.innerHTML = contents["Description_Event"];

    eventCard.append(h4);
    eventCard.append(p);

    container.append(eventCard);
  });
}

ajaxRequest("GET", "php/controllerAccueil.php/events", loadAccueil);
