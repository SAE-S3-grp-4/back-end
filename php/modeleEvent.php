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



function addToCart($db, $userId, $eventId): bool
{
    try {
        $verifExiste = 'SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :user_id AND Statut_Commande = "Dans le panier" AND Id_Commande = ANY (SELECT Id_Commande FROM INSCRIPTION WHERE Id_Event = :event_id);';
        $statement = $db->prepare($verifExiste);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {

            // Update the existing order
            $updateCommande = 'UPDATE COMMANDE SET Date_Commande = NOW() WHERE Id_Commande = :order_id;';
            $statement = $db->prepare($updateCommande);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->execute();

            // Update the existing Inscription
            $updateInscription = 'UPDATE INSCRIPTION SET Qte_Inscription = Qte_Inscription + 1 WHERE Id_Commande = :order_id AND Id_Event = :event_id;';
            $statement = $db->prepare($updateInscription);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
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


            // Create a Inscription
            $insertInscription = 'INSERT INTO INSCRIPTION (Qte_Inscription, Id_Commande, Id_Event) VALUES (1, :order_id, :event_id);';
            $statement = $db->prepare($insertInscription);
            $statement->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $statement->execute();
        }
        return true;
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}

function removeFromCart($db, $userId, $eventId): bool
{
    try {
        $verifExiste = 'SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :user_id AND Statut_Commande = "Dans le panier" AND Id_Commande = ANY (SELECT Id_Commande FROM INSCRIPTION WHERE Id_Event = :event_id);';
        $statement = $db->prepare($verifExiste);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {

            $verifQte = 'SELECT Qte_Inscription FROM INSCRIPTION WHERE Id_Commande = :order_id AND Id_Event = :event_id;';
            $statement = $db->prepare($verifQte);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $statement->execute();
            $resultQte = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($resultQte[0]['Qte_Inscription'] > 1) {
                // Update the existing Inscription
                $updateInscription = 'UPDATE INSCRIPTION SET Qte_Inscription = Qte_Inscription - 1 WHERE Id_Commande = :order_id AND Id_Event = :event_id;';
                $statement = $db->prepare($updateInscription);
                $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
                $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                $statement->execute();

                // Update the existing order
                $updateCommande = 'UPDATE COMMANDE SET Date_Commande = NOW() WHERE Id_Commande = :order_id;';
                $statement = $db->prepare($updateCommande);
                $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
                $statement->execute();
            } else {
                delFromCart($db, $userId, $eventId);
            }
        }else{
            return false;
        }
        return true;
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}

function delFromCart($db, $userId, $eventId): bool
{
    try {
        $verifExiste = 'SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :user_id AND Statut_Commande = "Dans le panier" AND Id_Commande = ANY (SELECT Id_Commande FROM INSCRIPTION WHERE Id_Event = :event_id);';
        $statement = $db->prepare($verifExiste);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {

            // Update the existing order
            $updateCommande = 'UPDATE COMMANDE SET Statut_commande = "Annulée" WHERE Id_Commande = :order_id;';
            $statement = $db->prepare($updateCommande);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->execute();

            // Delete the existing Inscription
            $deleteInscription = 'DELETE FROM INSCRIPTION WHERE Id_Commande = :order_id AND Id_Event = :event_id;';
            $statement = $db->prepare($deleteInscription);
            $statement->bindParam(':order_id', $result[0]['Id_Commande'], PDO::PARAM_INT);
            $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $statement->execute();

        }else{
            return false;
        }
        return true;
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}

function isSubscribed($db, $userId, $eventId): bool
{
    try {
        $verifExiste = 'SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :user_id AND Statut_Commande = "Dans le panier" AND Id_Commande = ANY (SELECT Id_Commande FROM INSCRIPTION WHERE Id_Event = :event_id);';
        $statement = $db->prepare($verifExiste);
        $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            return true;
        }else{
            return false;
        }
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}