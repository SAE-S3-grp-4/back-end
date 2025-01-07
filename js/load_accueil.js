function loadEvent(events) {
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

function loadProduit(produits) {
  console.log("Loading produits:", produits);
  let container = document.getElementsByClassName("shop-img")[0];
  container.innerHTML = ""; // Clear existing produits
  produits.forEach((contents) => {
    let p = document.createElement("p");
    p.innerHTML = contents["Nom_Produit"];

    let img = new Image();
    img.src = "img/imgProduits/" + encodeURIComponent(contents["Img_Produit"]);

    container.append(p);
    container.append(img);
  });
}

ajaxRequest("GET", "php/controllerAccueil.php/events", loadEvent);

ajaxRequest("GET", "php/controllerAccueil.php/produit", loadProduit);
