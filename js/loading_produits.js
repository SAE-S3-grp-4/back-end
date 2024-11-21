//------------------------------------------------------------------------------
//--- Add Product --------------------------------------------------------------
//------------------------------------------------------------------------------
document.getElementById("add-product").addEventListener('submit', (event) => 
    {
    event.preventDefault();
    let nom = document.getElementById('product-name').value;
    let description = document.getElementById('product-description').value;
    let prix = document.getElementById('product-price').value;
    let stock = document.getElementById('product-stock').value;
    let image = document.getElementById('product-image').value;

    console.log("Ajout d'un produit");

    let data = `nom=${encodeURIComponent(nom)}&description=${encodeURIComponent(description)}&image=${encodeURIComponent(image)}&prix=${encodeURIComponent(prix)}&stock=${encodeURIComponent(stock)}`;

    ajaxRequest('POST', 'php/request.php/produit/', () => {
        ajaxRequest('GET', 'php/request.php/produits', loadProduits);
    }, data);

    nom = document.getElementById('product-name').value = '';
    description = document.getElementById('product-description').value = '';
    prix = document.getElementById('product-price').value = '';
    stock = document.getElementById('product-stock').value = '';
    image = document.getElementById('product-image').value = '';
});




//------------------------------------------------------------------------------
//--- Load Product -------------------------------------------------------------
//------------------------------------------------------------------------------
function loadProduits(produits){
    //console.log(produits)
    let container = document.getElementById("product-list")

    produits.forEach((contents, idx) => {
        let d = document.createElement("div");
        d.className = 'product-card';

        let h3 = document.createElement("h3");
        h3.innerText = contents.Nom_Produit;

        let p = document.createElement("p");
        p.innerText = contents.Description_Produit;

        let img = new Image();
        img.src = "img/" + contents["Img_Produit"];
        img.alt = contents["Img_Produit"];
        img.setAttribute("photoid", idx);

        d.append(h3);
        d.append(p);
        d.append(img);

        let details = document.createElement("div");
        details.className = 'product-details';

        let sp = document.createElement("span");
        sp.innerText = contents.Prix_Produit + '€ ';

        let sp2 = document.createElement("span");
        sp2.innerText = contents.Stock_Produit + ' en stock';

        details.append(sp);
        details.append(sp2);

        let actions = document.createElement("div");
        actions.className = 'product-actions';

        let btndel = document.createElement("button");
        btndel.innerText = 'Supprimer'
        btndel.className = 'btn-delete';
        btndel.setAttribute("data-id", contents.Id_Produit);
        btndel.click = deleteProduct();

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


//------------------------------------------------------------------------------
//--- Delete Product -----------------------------------------------------------
//------------------------------------------------------------------------------


function deleteProduct() {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', (event) => {
            let productId = event.target.getAttribute('data-id');
            let data = `id=${productId}`
            ajaxRequest('DELETE', `php/request.php/produit?` + data, () => {
                ajaxRequest('GET', 'php/request.php/produits', loadProduits);
            });
        });
    });
}


ajaxRequest("GET","php/request.php/produits", produits => {
    loadProduits(produits);
    deleteProduct();
});