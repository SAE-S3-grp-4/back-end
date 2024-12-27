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

function dbRequestspreadsheet($db)
{
    try {
        $request = 'SELECT * FROM FEUILLE_DE_CALCUL';
        $statement = $db->prepare($request);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbAddSpreadsheet($db, $name, $file)
{
    try {
        $request = 'INSERT INTO FEUILLE_DE_CALCUL(Nom_Feu_Calc, Date_Feu_Calc, Fichier_Feu_Calc) VALUES(:Nom, NOW(), :Fichier)';
        $statement = $db->prepare($request);
        $statement->bindParam(':Nom', $name, PDO::PARAM_STR, 50);
        $statement->bindParam(':Fichier', $file, PDO::PARAM_STR, 255);
        error_log('Nom: ' . $name . ' Fichier: ' . $file);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbDeleteSpreadsheet($db, $id)
{
    try {
        // Delete the product itself
        $deleteSpreadsheet = 'DELETE FROM FEUILLE_DE_CALCUL WHERE Id_Feu_Calc = :id';
        $statement = $db->prepare($deleteSpreadsheet);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestSpreadsheetById($db, $id)
{
    try {
        $request = 'SELECT * FROM FEUILLE_DE_CALCUL WHERE Id_Feu_Calc = :id';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbModifySpreadsheet($db, $id, $nom, $file)
{
    try {
        if ($file) {
            $request = 'UPDATE FEUILLE_DE_CALCUL SET Nom_Feu_Calc = :Nom, Fichier_Feu_Calc = :File WHERE Id_Feu_Calc = :Id';
            $statement = $db->prepare($request);
            $statement->bindParam(':File', $file, PDO::PARAM_STR, 250);
        } else {
            $request = 'UPDATE FEUILLE_DE_CALCUL SET Nom_Feu_Calc = :Nom WHERE Id_Feu_Calc = :Id';
            $statement = $db->prepare($request);
        }
        $statement->bindParam(':Id', $id, PDO::PARAM_INT);
        $statement->bindParam(':Nom', $nom, PDO::PARAM_STR, 50);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

