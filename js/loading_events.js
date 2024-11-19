function loadEvents(events){
    console.log(events);
    let container = document.getElementById("event-list");

    events.forEach((contents, idx) => {
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
        btndel.innerText = 'Supprimer'
        btndel.className = 'btn-delete';

        let btnmod = document.createElement("button");
        btnmod.innerText = 'Modifier'
        btnmod.className = 'btn-modify';

        actions.append(btndel);
        actions.append(btnmod);

        d.append(details);
        d.append(actions);

        container.append(d);
    });
}

ajaxRequest("GET","php/request.php/events",loadEvents);