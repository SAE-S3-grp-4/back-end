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