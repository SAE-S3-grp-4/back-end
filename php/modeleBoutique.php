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

function addToCart($db, $userId, $productId)
{
    try {
        $checkUser = 'SELECT COUNT(*) FROM membre WHERE Id_Membre = :user_id';
        $statement = $db->prepare($checkUser);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
        if ($statement->fetchColumn() == 0) {
            error_log('User ID does not exist: ' . $userId);
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'User ID does not exist']);
            return false;
        }

        // Create a new order
        $insertCommande = 'INSERT INTO COMMANDE (Statut_Commande, Date_Commande, Id_Membre) VALUES (:statut, :date, :user_id);';
        $statement = $db->prepare($insertCommande);
        $statut = 'Dans le panier';
        $date = '1000-01-01 00:00:00.000000';
        $statement->bindParam(':date', $date, PDO::PARAM_STR);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':statut', $statut, PDO::PARAM_STR);
        $statement->execute();
        $orderId = $db->lastInsertId();

        // Debugging: Log the new order ID
        error_log('New order ID: ' . $orderId);

        // Create a Bon de commande
        $insertBonDeCommande = 'INSERT INTO BON_DE_COMMANDE (Qte_Produit, Id_Commande, Id_Produit) VALUES (1, :order_id, :product_id);';
        $statement = $db->prepare($insertBonDeCommande);
        $statement->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $statement->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $statement->execute();

        // Debugging: Log the product ID added to the order
        error_log('Product ID added to order: ' . $productId);

        return true;
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}