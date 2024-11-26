<?php

require_once('constantes.php');

function dbConnect()
{
    try {
        $db = new PDO(
            'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER,
            DB_PASSWORD
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $exception) {
        error_log('Connection error: ' . $exception->getMessage());
        return false;
    }
    return $db;
}

function dbRequestProduct($db)
{
    try {
        $request = 'SELECT * FROM PRODUIT';
        $statement = $db->prepare($request);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbAddProduct($db, $nom, $desc, $img, $prix, $stock)
{
    try {
        $request = 'INSERT INTO PRODUIT(Nom_Produit, Description_Produit, Img_Produit, Prix_Produit, Stock_Produit) VALUES(:Nom_Produit, :Description_Produit, :Img_Produit, :Prix_Produit, :Stock_Produit)';
        $statement = $db->prepare($request);
        $statement->bindParam(':Nom_Produit', $nom, PDO::PARAM_STR, 50);
        $statement->bindParam(':Description_Produit', $desc, PDO::PARAM_STR, 200);
        $statement->bindParam(':Img_Produit', $img, PDO::PARAM_STR, 200);
        $statement->bindParam(':Prix_Produit', $prix, PDO::PARAM_INT, 10);
        $statement->bindParam(':Stock_Produit', $stock, PDO::PARAM_INT, 10);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error:' . $exception->getMessage());
        return false;
    }
    return true;
}


function dbDeleteProduct($db, $id)
{
    try {
        // Update the status of related orders to "Produit supprimé"
        $updateOrderStatus = 'UPDATE COMMANDE SET Statut_Commande = "Produit supprimé" WHERE Id_Commande IN (SELECT Id_Commande FROM BON_DE_COMMANDE WHERE Id_Produit = :id)';
        $statement = $db->prepare($updateOrderStatus);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Delete related order slips
        $deleteOrderSlips = 'DELETE FROM BON_DE_COMMANDE WHERE Id_Produit = :id';
        $statement = $db->prepare($deleteOrderSlips);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Delete the product itself
        $deleteProduct = 'DELETE FROM PRODUIT WHERE Id_Produit = :id';
        $statement = $db->prepare($deleteProduct);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbModifyProduct($db, $id, $nom, $desc, $img, $prix, $stock)
{
    try {
        if ($img) {
            $request = 'UPDATE PRODUIT SET Nom_Produit = :Nom_Produit, Description_Produit = :Description_Produit, Img_Produit = :Img_Produit, Prix_Produit = :Prix_Produit, Stock_Produit = :Stock_Produit WHERE Id_Produit = :Id_Produit';
            echo $request;
            $statement = $db->prepare($request);
            $statement->bindParam(':Img_Produit', $img, PDO::PARAM_STR, 250);
        } else {
            $request = 'UPDATE PRODUIT SET Nom_Produit = :Nom_Produit, Description_Produit = :Description_Produit, Prix_Produit = :Prix_Produit, Stock_Produit = :Stock_Produit WHERE Id_Produit = :Id_Produit';
            $statement = $db->prepare($request);
        }
        $statement->bindParam(':Id_Produit', $id, PDO::PARAM_INT);
        $statement->bindParam(':Nom_Produit', $nom, PDO::PARAM_STR, 50);
        $statement->bindParam(':Description_Produit', $desc, PDO::PARAM_STR, 150);
        $statement->bindParam(':Prix_Produit', $prix, PDO::PARAM_STR, 15);
        $statement->bindParam(':Stock_Produit', $stock, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestProductById($db, $id)
{
    try {
        $request = 'SELECT * FROM PRODUIT WHERE Id_Produit = :Id_Produit';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Produit', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}