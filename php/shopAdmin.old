<form action="" method="POST">
    <div class="container">
        <div>
            <input type="text" id="nom" name="nom" placeholder="Nom" required>
            <input type="text" id="description" name="description" placeholder="Description">
            <input type="file" name="image" id="image">
            <input type="text" name="prix" id="prix" placeholder="prix" required>
            <input type="text" name="stock" id="stock" placeholder="stock" required>
        </div>
        <input type="submit" value="Envoyer">
    </div>
</form>


<?php
require 'database.php';

$db = dbConnect();

$products = dbRequestProduct($db);
foreach ($products as $product) {
    echo $product['Nom_Produit'] . ' ';
    echo $product['Prix_Produit'] . ' ';
    echo $product['Description_Produit'] . ' ';
    echo $product['Stock_Produit'] . ' ';
    echo $product['Img_Produit'] . ' ';
    echo '<br>';
}
?>