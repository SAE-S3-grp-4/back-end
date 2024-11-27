document.getElementById("add-product").addEventListener('submit', (event) => {
    event.preventDefault();
    let nom = document.getElementById('product-name').value;
    let description = document.getElementById('product-description').value;
    let prix = document.getElementById('product-price').value;
    let stock = document.getElementById('product-stock').value;
    let image = document.getElementById('product-image').files[0];

    let formData = new FormData();
    formData.append('nom', nom);
    formData.append('description', description);
    formData.append('prix', prix);
    formData.append('stock', stock);
    formData.append('image', image);

    console.log("Ajout d'un produit");


    ajaxRequest('POST', 'php/controllerGestionBoutique.php/produit/', () => {
        ajaxRequest('GET', 'php/controllerGestionBoutique.php/produits', loadProduits);
    }, formData);

    document.getElementById('product-name').value = '';
    document.getElementById('product-description').value = '';
    document.getElementById('product-price').value = '';
    document.getElementById('product-stock').value = '';
    document.getElementById('product-image').value = '';
});

document.getElementById("product-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-delete')) {
        const productId = parseInt(event.target.dataset.id, 10);
        if (!isNaN(productId)) {
            ajaxRequest('DELETE', `php/controllerGestionBoutique.php/produit/${productId}`, () => {
                ajaxRequest('GET', 'php/controllerGestionBoutique.php/produits', loadProduits);
            });
        } else {
            console.error('Invalid product ID');
        }
    }
});

document.getElementById("product-list").addEventListener('click', (event) => {
    if (event.target.classList.contains('btn-modify')) {
        const productId = parseInt(event.target.getAttribute('data-id'), 10);
        if (!isNaN(productId)) {
            ajaxRequest('GET', `php/controllerGestionBoutique.php/produit/${productId}`, (product) => {
                document.getElementById('modify-product-id').value = product.Id_Produit;
                document.getElementById('modify-product-name').value = product.Nom_Produit;
                document.getElementById('modify-product-description').value = product.Description_Produit;
                document.getElementById('modify-product-price').value = product.Prix_Produit;
                document.getElementById('modify-product-stock').value = product.Stock_Produit;
                document.getElementById('modify-product').style.display = 'block';

                // Scroll to the modification form
                document.getElementById('modify-product').scrollIntoView({ behavior: 'smooth' });
            });
        } else {
            console.error('Invalid product ID');
        }
    }
});

document.getElementById("form-modify-product").addEventListener('submit', (event) => {
    event.preventDefault();

    let id = parseInt(document.getElementById('modify-product-id').value, 10);
    let nom = document.getElementById('modify-product-name').value.trim();
    let description = document.getElementById('modify-product-description').value.trim();
    let prix = parseFloat(document.getElementById('modify-product-price').value);
    let stock = parseInt(document.getElementById('modify-product-stock').value, 10);
    let image = document.getElementById('modify-product-image').files[0];

    // Validation des types
    if (!id || !nom || !description || isNaN(prix) || isNaN(stock)) {
        alert("Veuillez vérifier les champs. Tous doivent être correctement remplis.");
        return;
    }

    // Préparation des données
    let formData = new FormData();
    formData.append('id', id);
    formData.append('nom', nom);
    formData.append('description', description);
    formData.append('prix', prix.toFixed(2)); // Prix formaté avec 2 décimales
    formData.append('stock', stock);
    if (image) {
        formData.append('image', image);
    }

    console.log("Modification du produit", id, nom, description, prix, stock, image);
    console.log("Données du formulaire:", formData);

    // Envoyer la requête
    ajaxRequest('POST', 'php/controllerGestionBoutique.php/produit-modify', (response) => {
        console.log('Réponse du serveur:', response);
        ajaxRequest('GET', 'php/controllerGestionBoutique.php/produits', loadProduits); // Rafraîchit la liste des produits
        document.getElementById('modify-product').style.display = 'none';
    }, formData);
});




//------------------------------------------------------------------------------
//--- Load Product -------------------------------------------------------------
//------------------------------------------------------------------------------
function loadProduits(produits) {
    if (!Array.isArray(produits)) {
        console.error("Invalid products data: expected an array");
        return;
    }
    let container = document.getElementById("product-list");
    container.innerHTML = ''; // Clear existing products

    produits.forEach((product) => {
        let productCard = document.createElement("div");
        productCard.className = 'product-card';

        let productName = document.createElement("h3");
        productName.innerText = product.Nom_Produit || "Nom inconnu";

        let productDescription = document.createElement("p");
        productDescription.innerText = product.Description_Produit || "Pas de description";

        let productImage = new Image();
        productImage.src = "img/imgProduits/" + encodeURIComponent(product.Img_Produit || "default.png");
        productImage.alt = product.Img_Produit || "Image manquante";

        let productDetails = document.createElement("div");
        productDetails.className = 'product-details';

        let productPrice = document.createElement("span");
        productPrice.innerText = product.Prix_Produit + '€ ';

        let productStock = document.createElement("span");
        productStock.innerText = product.Stock_Produit + ' en stock';

        productDetails.append(productPrice);
        productDetails.append(productStock);

        let productActions = document.createElement("div");
        productActions.className = 'product-actions';

        let deleteButton = document.createElement("button");
        deleteButton.innerText = 'Supprimer';
        deleteButton.className = 'btn-delete';
        deleteButton.setAttribute("data-id", product.Id_Produit);

        let modifyButton = document.createElement("button");
        modifyButton.innerText = 'Modifier';
        modifyButton.className = 'btn-modify';
        modifyButton.setAttribute("data-id", product.Id_Produit);


        productActions.append(deleteButton);
        productActions.append(modifyButton);

        productCard.append(productName);
        productCard.append(productDescription);
        productCard.append(productImage);
        productCard.append(productDetails);
        productCard.append(productActions);

        container.append(productCard);
    });
}

ajaxRequest("GET","php/controllerGestionBoutique.php/produits", loadProduits);