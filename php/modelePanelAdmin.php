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

function dbRequestStudentDetails($db, $id)
{
    try {
        $request = '
            SELECT 
                MEMBRE.Id_Membre, MEMBRE.Nom_Membre, MEMBRE.Prenom_Membre, MEMBRE.Pseudo_Membre, MEMBRE.Mail_Membre, MEMBRE.Grp_Membre, MEMBRE.Pdp_Membre, 
                COALESCE(GRADE.Nom_Grade, "Aucun") AS Nom_Grade, 
                ROLE.Nom_Role 
            FROM MEMBRE
            LEFT JOIN GRADE ON MEMBRE.Id_Grade = GRADE.Id_Grade
            LEFT JOIN ROLE ON MEMBRE.Id_Role = ROLE.Id_Role
            WHERE MEMBRE.Id_Membre = :Id_Membre
        ';
        $statement = $db->prepare($request);
        $statement->bindParam(':Id_Membre', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}

function dbRequestStudents($db)
{
    try {
        $request = 'SELECT * FROM MEMBRE';
        $statement = $db->prepare($request);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return $result;
}


function deleteStudentById($db, $id)
{
    try {
        $db->beginTransaction();

        $request = "DELETE FROM INSCRIPTION WHERE Id_Commande IN (SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :id)";
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        error_log('Deleted INSCRIPTION');

        $request = "DELETE FROM BON_DE_COMMANDE WHERE Id_Commande IN (SELECT Id_Commande FROM COMMANDE WHERE Id_Membre = :id)";
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        error_log('Deleted BON_DE_COMMANDE');

        // Supprimer les commandes de l'étudiant
        $request = "DELETE FROM COMMANDE WHERE Id_Membre = :id";
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        error_log('Deleted COMMANDE');

        // Supprimer l'étudiant
        $request = "DELETE FROM MEMBRE WHERE Id_Membre = :id";
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        error_log('Deleted MEMBRE');

        $db->commit();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}

function dbRequestStudentRegistrations($db, $id)
{
    try {
        $request = 'SELECT * FROM INSCRIPTION JOIN EVENEMENT ON INSCRIPTION.Id_Event = EVENEMENT.Id_Event JOIN COMMANDE ON INSCRIPTION.Id_Commande = COMMANDE.Id_Commande WHERE COMMANDE.Id_Membre = :id';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
}

function updateStudentRole($db, $id, $role)
{
    try {
        $request = '
            UPDATE MEMBRE
            SET Id_Role = (SELECT Id_Role FROM ROLE WHERE Nom_Role = :role)
            WHERE Id_Membre = :id
        ';
        $statement = $db->prepare($request);
        $statement->bindParam(':role', $role, PDO::PARAM_STR);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        return $statement->execute();
    } catch (PDOException $exception) {
        error_log('Role update error: ' . $exception->getMessage());
        return false;
    }
}
