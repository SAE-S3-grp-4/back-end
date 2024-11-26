function loadProducts() {
    ajaxRequest('GET', 'php/controllerBoutique.php/produits', (produits) => {
        if (!Array.isArray(produits)) {
            console.error("Invalid products data: expected an array");
            return;
        }
        let container = document.querySelector(".product-list");
        container.innerHTML = ''; // Clear existing products

        produits.forEach((product) => {
            let productCard = document.createElement("div");
            productCard.className = 'product-card';

            let productName = document.createElement("h3");
            productName.innerText = product.Nom_Produit || "Nom inconnu";

            let productDescription = document.createElement("p");
            productDescription.innerText = product.Description_Produit || "Pas de description";

            let productImage = new Image();
            productImage.src = "imgProduits/" + encodeURIComponent(product.Img_Produit || "default.png");
            productImage.alt = product.Img_Produit || "Image manquante";

            let productDetails = document.createElement("div");
            productDetails.className = 'product-details';

            let productPrice = document.createElement("span");
            productPrice.innerText = product.Prix_Produit + '€ ';

            productDetails.append(productPrice);

            let productActions = document.createElement("div");
            productActions.className = 'product-actions';

            let addToCartButton = document.createElement("button");
            addToCartButton.innerText = 'Ajouter au panier';
            addToCartButton.className = 'btn-add-to-cart';
            addToCartButton.setAttribute("data-id", product.Id_Produit);

            productActions.append(addToCartButton);

            productCard.append(productName);
            productCard.append(productDescription);
            productCard.append(productImage);
            productCard.append(productDetails);
            productCard.append(productActions);

            container.append(productCard);
        });
    });
}

ajaxRequest("GET","php/controllerBoutique.php/produits", loadProducts());