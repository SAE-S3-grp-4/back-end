<?php
// Database connection
require_once('constantes.php');
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');

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

// Fetch all products
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

function addToCart($db, $userId, $productId): bool
{
    try {
        $verifExiste = 'SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :user_id AND Statut_Commande = "Dans le panier" AND Id_Commande = ANY (SELECT Id_Commande FROM BON_DE_COMMANDE WHERE Id_Produit = :product_id);';
        $statement = $db->prepare($verifExiste);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {

            // Update the existing order
            $updateCommande = 'UPDATE COMMANDE SET Date_Commande = NOW() WHERE Id_Commande = :order_id;';
            $statement = $db->prepare($updateCommande);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->execute();

            // Update the existing Bon de commande
            $updateBonDeCommande = 'UPDATE BON_DE_COMMANDE SET Qte_Produit = Qte_Produit + 1 WHERE Id_Commande = :order_id AND Id_Produit = :product_id;';
            $statement = $db->prepare($updateBonDeCommande);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $statement->execute();

        }else{
            // Create a new order
            $insertCommande = 'INSERT INTO COMMANDE (Statut_Commande, Date_Commande, Id_Membre) VALUES (:statut, NOW(), :user_id);';
            $statement = $db->prepare($insertCommande);
            $statut = 'Dans le panier';
            $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindParam(':statut', $statut, PDO::PARAM_STR);
            $statement->execute();
            $orderId = $db->lastInsertId();


            // Create a Bon de commande
            $insertBonDeCommande = 'INSERT INTO BON_DE_COMMANDE (Qte_Produit, Id_Commande, Id_Produit) VALUES (1, :order_id, :product_id);';
            $statement = $db->prepare($insertBonDeCommande);
            $statement->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $statement->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $statement->execute();
        }
        return true;
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}