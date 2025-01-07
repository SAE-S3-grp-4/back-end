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

function dbRequestEvent($db)
{
    try {
        $request = 'SELECT * FROM EVENEMENT';
        $statement = $db->prepare($request);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbAddEvent($db, $nom, $desc, $date, $prix, $nbPlace, $dateFinInscription)
{
    try {
        $request = 'INSERT INTO EVENEMENT (Nom_Event, Description_Event, Date_Event, Prix_Event, Nb_Place_Event, Date_Fin_Inscription) 
                    VALUES (:Nom_Event, :Description_Event, :Date_Event, :Prix_Event, :Nb_Place_Event, :Date_Fin_Inscription)';
        $statement = $db->prepare($request);
        $statement->bindParam(':Nom_Event', $nom, PDO::PARAM_STR);
        $statement->bindParam(':Description_Event', $desc, PDO::PARAM_STR);
        $statement->bindParam(':Date_Event', $date, PDO::PARAM_STR);
        $statement->bindParam(':Prix_Event', $prix, PDO::PARAM_INT);
        $statement->bindParam(':Nb_Place_Event', $nbPlace, PDO::PARAM_INT);
        $statement->bindParam(':Date_Fin_Inscription', $dateFinInscription, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbDeleteEvent($db, $id)
{
    try {
        // First, update the status of the related commands to 'Annulée'
        $updateCommandStatus = 'UPDATE COMMANDE SET Statut_Commande = "Annulée" WHERE Id_Commande IN (SELECT Id_Commande FROM INSCRIPTION WHERE Id_Event = :id)';
        $statement = $db->prepare($updateCommandStatus);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Then, delete the inscriptions related to the event
        $deleteInscriptions = 'DELETE FROM INSCRIPTION WHERE Id_Event = :id';
        $statement = $db->prepare($deleteInscriptions);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        // Finally, delete the event itself
        $deleteEvent = 'DELETE FROM EVENEMENT WHERE Id_Event = :id';
        $statement = $db->prepare($deleteEvent);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbModifyEvent($db, $id, $nom, $desc, $date, $prix, $nbPlace, $dateFinInscription)
{
    try {
        $request = 'UPDATE EVENEMENT SET Nom_Event = :Nom_Event, Description_Event = :Description_Event, Date_Event = :Date_Event, Prix_Event = :Prix_Event, Nb_Place_Event = :Nb_Place_Event, Date_Fin_Inscription = :Date_Fin_Inscription WHERE Id_Event = :Id_Event';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Event', $id, PDO::PARAM_INT);
        $statement->bindParam(':Nom_Event', $nom, PDO::PARAM_STR);
        $statement->bindParam(':Description_Event', $desc, PDO::PARAM_STR);
        $statement->bindParam(':Date_Event', $date, PDO::PARAM_STR);
        $statement->bindParam(':Prix_Event', $prix, PDO::PARAM_INT);
        $statement->bindParam(':Nb_Place_Event', $nbPlace, PDO::PARAM_INT);
        $statement->bindParam(':Date_Fin_Inscription', $dateFinInscription, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestEventById($db, $id)
{
    try {
        $request = 'SELECT * FROM EVENEMENT WHERE Id_Event = :Id_Event';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Event', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbRequest3FirstEvent($db)
{
    try {
        $request ='SELECT Nom_Event, Description_Event FROM EVENEMENT WHERE Date_Event > NOW() ORDER BY Date_Event ASC LIMIT 3';
        $statement = $db->prepare($request);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}