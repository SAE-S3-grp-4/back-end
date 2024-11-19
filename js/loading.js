/* document.getElementById("comments-add").addEventListener('submit', (event) =>
{
    event.preventDefault();
    let value = document.getElementById('comment-field').value;
    document.getElementById('comment-field').value = '';
    ajaxRequest('POST', 'php/request.php/comments/', () =>
    {
        ajaxRequest('GET', 'php/request.php/comments/?photoId=' + currentID, loadComments);
    }, 'userLogin=' + login + '&photoId=' + currentID + '&comment=' + value);

});

*/

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
        ajaxRequest('GET', 'php/request.php/produits?', loadProduits);
    }, data);

    nom = document.getElementById('product-name').value = '';
    description = document.getElementById('product-description').value = '';
    prix = document.getElementById('product-price').value = '';
    stock = document.getElementById('product-stock').value = '';
    image = document.getElementById('product-image').value = '';
});

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




/* function loadPhotos(photos){
    console.log(photos);
    let container = document.getElementById("thumbnails");

    photos.forEach((contents, idx) => {
        let a = document.createElement("a");
        a.href = "#"

        let d = document.createElement("div");
        d.className = 'col-xs-2 col-md-2';

        let img = new Image();
        img.src = contents["small"];
        img.alt = contents["small"];
        img.id = "thumbnail-" + idx;
        img.setAttribute("photoid", idx);
        img.className = "img-thumbnail";

        a.append(img);
        d.append(a);
        container.append(d);
    });

}

function loadPhoto(photo){
    console.log(photo);

    let photoDiv = document.getElementById("photo");

    let pDiv = document.createElement("div");
    pDiv.className = "card col-xs-12 col-md-12";

    let cardBodyDIV = document.createElement("div");
    cardBodyDIV.className = "card-body";

    let H4D = document.createElement("h4");
    H4D.innerHTML = photo["title"];

    let img = new Image();
    img.src = photo["large"];
    img.className = "img-thumbnail";

    H4D.append(img);
    cardBodyDIV.append(H4D);
    pDiv.append(cardBodyDIV);
    photoDiv.innerHTML = "<div class=\"card col-xs-12 col-md-12\">\n" + pDiv.innerHTML + "</div>";

}


function modifyComments()
{
    const modifyButtons = document.querySelectorAll('.mod');
    modifyButtons.forEach(e => e.addEventListener('click', (event) =>
    {
        let value = event.target.closest('.mod').getAttribute('value');
        ajaxRequest('PUT', 'php/request.php/comments/' + value, () =>
        {
            ajaxRequest('GET', 'php/request.php/comments/?photoId=' + currentID, loadComments);
        }, '&userLogin=' + login + '&comment=' + prompt('Nouveau tweet :'));
    }));
}

//------------------------------------------------------------------------------
//--- DeleteTweets -------------------------------------------------------------
//------------------------------------------------------------------------------
// Delete tweets.
function deleteComments()
{
    const deleteButtons = document.querySelectorAll('.del');
    deleteButtons.forEach(e => e.addEventListener('click', (event) =>
    {
        let value = event.target.closest('.del').getAttribute('value');
        ajaxRequest('DELETE', 'php/request.php/comments/' + value + '?userLogin=' +
            login, () =>
        {
            ajaxRequest('GET', 'php/request.php/comments/?photoId=' + currentID, loadComments);
        });
    }));
}


function loadComments(comments){
    document.getElementById("comments-add").style.display = "block";
    document.getElementById('comments').innerHTML = '<h3>' + currentTitle + '</h3>';
    for (let tweet of comments)
        document.getElementById('comments').innerHTML += '<div class="card">' +
            '<div class="card-body">' + tweet.userLogin + ' : ' + tweet.comment +
            '<div class="btn-group float-end" role="group">' +
            '<button type="button" class="btn btn-light float-end mod"' +
            ' value="' + tweet.id + '"><i class="fa fa-edit"></i></button>' +
            '<button type="button" class="btn btn-light float-end del"' +
            ' value="' + tweet.id + '"><i class="fa fa-trash"></i></button>' +
            '<div></div></div>';
    modifyComments();
    deleteComments();
}

let currentID = 0;
function requestPhoto(){
    let id = document.getElementById(event.target.id).getAttribute('photoid');
    id = parseInt(id) + 1;
    currentID = id;
    ajaxRequest("GET","php/request.php/photos/" + id,loadPhoto);
    ajaxRequest("GET", "php/request.php/comments/?photoId=" + id, loadComments);
}




document.getElementById('thumbnails').addEventListener('click', requestPhoto);
*/
ajaxRequest("GET","php/request.php/produits",loadProduits);