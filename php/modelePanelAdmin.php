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

function dbRequestStudentDetails($db, $id)
{
    try {
        $request = '
            SELECT 
                MEMBRE.Id_Membre, MEMBRE.Nom_Membre, MEMBRE.Mail_Membre, MEMBRE.Grp_Membre, MEMBRE.Pdp_Membre, 
                COALESCE(GRADE.Nom_Grade, "Aucun grade") AS Nom_Grade, 
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
