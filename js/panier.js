



let prixTotal = 0;

function loadCommandesProduits(commandes) {
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
        commandeNameCell.innerText = commande.Nom_Produit || "Nom inconnu";

        let commandePrixUCell = document.createElement("td");
        commandePrixUCell.innerText = commande.Prix_Produit + ' € ';

        let commandeQteCell = document.createElement("td");
        commandeQteCell.className = 'commande-qte';

        let commandeQteAction = document.createElement("div");
        commandeQteAction.className = 'quantity-buttons';

        let rmButton = document.createElement("button");
        rmButton.innerText = '-';
        rmButton.className = 'btn-remove';
        rmButton.setAttribute("product-id", commande.Id_Produit);
        rmButton.setAttribute("commande-id", commande.Id_Commande);

        let qteProduit = document.createElement("span");
        qteProduit.innerText = commande.Qte_Produit;

        let addButton = document.createElement("button");
        addButton.innerText = '+';
        addButton.className = 'btn-add';
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
        deleteButton.className = 'delete-btn';
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

    let prixTotalPanier = document.getElementById("prix-total");
    prixTotalPanier.innerHTML = prixTotal + ' €';

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
        rmButton.className = 'btn-remove';
        rmButton.setAttribute("product-id", commande.Id_Event);
        rmButton.setAttribute("commande-id", commande.Id_Commande);

        let qteProduit = document.createElement("span");
        qteProduit.innerText = commande.Qte_Inscription;

        let addButton = document.createElement("button");
        addButton.innerText = '+';
        addButton.className = 'btn-add';
        addButton.setAttribute("product-id", commande.Id_Event);
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
        deleteButton.className = 'delete-btn';
        deleteButton.setAttribute("product-id", commande.Id_Event);
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
ajaxRequest("GET","php/controllerPanier.php/commandes-events", loadCommandesEvents);
