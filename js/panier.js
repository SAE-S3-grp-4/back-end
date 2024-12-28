document.getElementById("cart-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-add-product')) {
        const productId = parseInt(event.target.getAttribute('product-id'), 10);
        if (!isNaN(productId)) {
                ajaxRequest('GET', `php/controllerBoutique.php/addToCart/${productId}`, (response) => {
                    if (response) {
                        ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);
                    } else {
                        alert('Erreur lors de la modification du panier');
                    }
                }, {productId: productId});
        } else {
            console.error('Invalid product ID');
        }
    } else if (event.target.classList.contains('btn-remove-product')) {
        const productId = parseInt(event.target.getAttribute('product-id'), 10);
        if (!isNaN(productId)) {
                ajaxRequest('GET', `php/controllerBoutique.php/removeFromCart/${productId}`, (response) => {
                    if (response) {
                        ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);
                    } else {
                        alert('Erreur lors de la modification du panier');
                    }
                }, {productId: productId});
        } else {
            console.error('Invalid product ID');
        }
    } else if (event.target.classList.contains('btn-delete-product')) {
        const productId = parseInt(event.target.getAttribute('product-id'), 10);
        if (!isNaN(productId)) {
                ajaxRequest('GET', `php/controllerBoutique.php/deleteFromCart/${productId}`, (response) => {
                    if (response) {
                        ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);
                    } else {
                        alert('Erreur lors de la modification du panier');
                    }
                }, {productId: productId});
        } else {
            console.error('Invalid product ID');
        }
    } else if (event.target.classList.contains('btn-add-event')) {
        const eventId = parseInt(event.target.getAttribute('event-id'), 10);
        if (!isNaN(eventId)) {
                ajaxRequest('GET', `php/controllerEvent.php/addToCart/${eventId}`, (response) => {
                    if (response) {
                        ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);
                    } else {
                        alert('Erreur lors de la modification du panier');
                    }
                }, {eventId: eventId});
        } else {
            console.error('Invalid event ID');
        }
    } else if (event.target.classList.contains('btn-remove-event')) {
        const eventId = parseInt(event.target.getAttribute('event-id'), 10);
        if (!isNaN(eventId)) {
                ajaxRequest('GET', `php/controllerEvent.php/removeFromCart/${eventId}`, (response) => {
                    if (response) {
                        ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);
                    } else {
                        alert('Erreur lors de la modification du panier');
                    }
                }, {eventId: eventId});
        } else {
            console.error('Invalid event ID');
        }
    } else if (event.target.classList.contains('btn-delete-event')) {
        const eventId = parseInt(event.target.getAttribute('event-id'), 10);
        if (!isNaN(eventId)) {
                ajaxRequest('GET', `php/controllerEvent.php/delFromCart/${eventId}`, (response) => {
                    if (response) {
                        ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);
                    } else {
                        alert('Erreur lors de la modification du panier');
                    }
                }, {eventId: eventId});
        } else {
            console.error('Invalid event ID');
        }
    }
});





function loadCommandesProduits(commandes) {
    if (!Array.isArray(commandes)) {
        console.error("Invalid products data: expected an array");
        return;
    }


    let container = document.getElementById("cart-list");
    container.innerHTML = ''; // Clear existing commandes

    prixTotal = 0;

    commandes.forEach((commande) => {

        let commandRow = document.createElement("tr");
        commandRow.className = 'commande-row';

        let commandeNameCell = document.createElement("td");
        commandeNameCell.innerText = commande.Nom_Produit || "Nom inconnu";

        let commandePrixUCell = document.createElement("td");
        commandePrixUCell.innerText = commande.Prix_Produit + ' € ';

        let commandeQteCell = document.createElement("td");
        commandeQteCell.className = 'commande-qte';

        let commandeQteAction = document.createElement("div");
        commandeQteAction.className = 'quantity-buttons';

        let rmButton = document.createElement("button");
        rmButton.innerText = '-';
        rmButton.className = 'btn-remove-product';
        rmButton.setAttribute("product-id", commande.Id_Produit);
        rmButton.setAttribute("commande-id", commande.Id_Commande);

        let qteProduit = document.createElement("span");
        qteProduit.innerText = commande.Qte_Produit;

        let addButton = document.createElement("button");
        addButton.innerText = '+';
        addButton.className = 'btn-add-product';
        addButton.setAttribute("product-id", commande.Id_Produit);
        addButton.setAttribute("commande-id", commande.Id_Commande);

        commandeQteAction.append(rmButton);
        commandeQteAction.append(qteProduit);
        commandeQteAction.append(addButton);

        commandeQteCell.append(commandeQteAction);

        let commandePrixTCell = document.createElement("td");
        commandePrixTCell.innerText = commande.Prix_Produit * commande.Qte_Produit + ' €';

        let commandeDeleteCell = document.createElement("td");
        commandeDeleteCell.className = 'commande-delete';

        let deleteButton = document.createElement("button");
        deleteButton.innerText = '🗑️';
        deleteButton.className = 'btn-delete-product';
        deleteButton.setAttribute("product-id", commande.Id_Produit);
        deleteButton.setAttribute("commande-id", commande.Id_Commande);

        commandeDeleteCell.append(deleteButton);

        commandRow.append(commandeNameCell);
        commandRow.append(commandePrixUCell);
        commandRow.append(commandeQteCell);
        commandRow.append(commandePrixTCell);
        commandRow.append(commandeDeleteCell);

        container.append(commandRow);

        prixTotal += commande.Prix_Produit * commande.Qte_Produit;
    });

    ajaxRequest("GET","php/controllerPanier.php/commandes-events", loadCommandesEvents);

}

function loadCommandesEvents(commandes) {
    if (!Array.isArray(commandes)) {
        console.error("Invalid products data: expected an array");
        return;
    }


    let container = document.getElementById("cart-list");
    //container.innerHTML = ''; // Clear existing commandes


    commandes.forEach((commande) => {

        let commandRow = document.createElement("tr");
        commandRow.className = 'commande-row';

        let commandeNameCell = document.createElement("td");
        commandeNameCell.innerText = commande.Nom_Event || "Nom inconnu";

        let commandePrixUCell = document.createElement("td");
        commandePrixUCell.innerText = commande.Prix_Event + ' € ';

        let commandeQteCell = document.createElement("td");
        commandeQteCell.className = 'commande-qte';

        let commandeQteAction = document.createElement("div");
        commandeQteAction.className = 'quantity-buttons';

        let rmButton = document.createElement("button");
        rmButton.innerText = '-';
        rmButton.className = 'btn-remove-event';
        rmButton.setAttribute("event-id", commande.Id_Event);
        rmButton.setAttribute("commande-id", commande.Id_Commande);

        let qteProduit = document.createElement("span");
        qteProduit.innerText = commande.Qte_Inscription;

        let addButton = document.createElement("button");
        addButton.innerText = '+';
        addButton.className = 'btn-add-event';
        addButton.setAttribute("event-id", commande.Id_Event);
        addButton.setAttribute("commande-id", commande.Id_Commande);

        commandeQteAction.append(rmButton);
        commandeQteAction.append(qteProduit);
        commandeQteAction.append(addButton);

        commandeQteCell.append(commandeQteAction);

        let commandePrixTCell = document.createElement("td");
        commandePrixTCell.innerText = commande.Prix_Event * commande.Qte_Inscription + ' €';

        let commandeDeleteCell = document.createElement("td");
        commandeDeleteCell.className = 'commande-delete';

        let deleteButton = document.createElement("button");
        deleteButton.innerText = '🗑️';
        deleteButton.className = 'btn-delete-event';
        deleteButton.setAttribute("event-id", commande.Id_Event);
        deleteButton.setAttribute("commande-id", commande.Id_Commande);

        commandeDeleteCell.append(deleteButton);

        commandRow.append(commandeNameCell);
        commandRow.append(commandePrixUCell);
        commandRow.append(commandeQteCell);
        commandRow.append(commandePrixTCell);
        commandRow.append(commandeDeleteCell);

        container.append(commandRow);

        prixTotal += commande.Prix_Event * commande.Qte_Inscription;
    });

    let prixTotalPanier = document.getElementById("prix-total");
    prixTotalPanier.innerHTML = prixTotal + ' €';

}



ajaxRequest("GET","php/controllerPanier.php/commandes-produits", loadCommandesProduits);

