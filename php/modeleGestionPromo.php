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

function dbRequestPromo($db)
{
    try {
        $request = 'SELECT * FROM PROMO';
        $statement = $db->prepare($request);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbAddPromo($db, $nom, $pourcentage) {
    try {
        // Vérifier si le nom du code promo existe déjà
        $checkRequest = 'SELECT COUNT(*) FROM PROMO WHERE Nom_Promo = :Nom_Promo';
        $checkStatement = $db->prepare($checkRequest);
        $checkStatement->bindParam(':Nom_Promo', $nom, PDO::PARAM_STR);
        $checkStatement->execute();
        $count = $checkStatement->fetchColumn();

        if ($count > 0) {
            return ['success' => false, 'error' => 'Le code promo existe déjà'];
        }

        // Insérer le nouveau code promo
        $request = 'INSERT INTO PROMO (Nom_Promo, Pourcentage_Promo) VALUES (:Nom_Promo, :Pourcentage_Promo)';
        $statement = $db->prepare($request);
        $statement->bindParam(':Nom_Promo', $nom, PDO::PARAM_STR);
        $statement->bindParam(':Pourcentage_Promo', $pourcentage, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return ['success' => false, 'error' => 'Database error'];
    }
    return ['success' => true];
}

function dbModifyPromo($db, $id, $nom, $pourcentage) {
    try {
        $request = 'UPDATE PROMO SET Nom_Promo = :Nom_Promo, Pourcentage_Promo = :Pourcentage_Promo WHERE Id_Promo = :Id_Promo';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Promo', $id, PDO::PARAM_INT);
        $statement->bindParam(':Nom_Promo', $nom, PDO::PARAM_STR);
        $statement->bindParam(':Pourcentage_Promo', $pourcentage, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbDeletePromo($db, $id) {
    try {
        // Mettre à jour les commandes pour définir Id_Promo à NULL
        $updateCommandes = 'UPDATE COMMANDE SET Id_Promo = NULL WHERE Id_Promo = :Id_Promo';
        $statement = $db->prepare($updateCommandes);
        $statement->bindParam(':Id_Promo', $id, PDO::PARAM_INT);
        $statement->execute();

        // Supprimer la promo
        $deletePromo = 'DELETE FROM PROMO WHERE Id_Promo = :Id_Promo';
        $statement = $db->prepare($deletePromo);
        $statement->bindParam(':Id_Promo', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestPromoById($db, $id) {
    try {
        $request = 'SELECT * FROM PROMO WHERE Id_Promo = :Id_Promo';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Promo', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}