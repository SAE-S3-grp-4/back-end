<?php
// Database connection
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

function addToCart($userId, $productId) {
    $pdo = dbConnect();
    try {
        $pdo->beginTransaction();

        // Create a new order
        $stmt = $pdo->prepare("INSERT INTO COMMANDE (Statut_Commande, Date_Commande, Id_Membre) VALUES ('Dans le panier', NOW(), :user_id)");
        $stmt->execute(['user_id' => $userId]);
        $orderId = $pdo->lastInsertId();

        // Create a corresponding order slip
        $stmt = $pdo->prepare("INSERT INTO BON_DE_COMMANDE (Qte_Produit, Id_Commande, Id_Produit) VALUES (1, :order_id, :product_id)");
        $stmt->execute(['order_id' => $orderId, 'product_id' => $productId]);

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('Add to cart error: ' . $e->getMessage());
        return false;
    }
}