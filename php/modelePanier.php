<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');
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

function dbRequestProductCommande($db, $id)
{
    $status = "Dans le panier";
    try {
        $request = 'SELECT * FROM COMMANDE JOIN BON_DE_COMMANDE ON COMMANDE.Id_Commande=BON_DE_COMMANDE.Id_Commande JOIN PRODUIT ON PRODUIT.Id_Produit=BON_DE_COMMANDE.Id_Produit WHERE COMMANDE.Id_Membre=:id AND COMMANDE.Statut_Commande=:statut';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':statut', $status, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbRequestEventCommande($db, $id)
{
    $status = "Dans le panier";
    try {
        $request = 'SELECT * FROM COMMANDE JOIN INSCRIPTION ON COMMANDE.Id_Commande=INSCRIPTION.Id_Commande JOIN EVENEMENT ON EVENEMENT.Id_Event=INSCRIPTION.Id_Event WHERE COMMANDE.Id_Membre=:id AND COMMANDE.Statut_Commande=:statut';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':statut', $status, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}