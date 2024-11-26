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

function dbRequestUser($db)
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

function dbLoginUser($db, $username, $password)
{
    try {
        $request = 'SELECT * FROM MEMBRE WHERE Nom_Membre = :username OR Mail_Membre = :username';
        $statement = $db->prepare($request);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Mdp_Membre'])) {
            return $user;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        error_log('Login error: ' . $exception->getMessage());
        return false;
    }
}

function dbRegisterUser($db, $name, $email, $password, $group)
{
    try {
        $request = 'INSERT INTO MEMBRE (Nom_Membre, Mail_Membre, Mdp_Membre, Grp_Membre, Id_Role) VALUES (:name, :email, :password, :group, 1)';
        $statement = $db->prepare($request);
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':group', $group, PDO::PARAM_STR);
        $statement->execute();
        return true;
    } catch (PDOException $exception) {
        error_log('Registration error: ' . $exception->getMessage());
        return false;
    }
}

function dbUserExists($db, $name, $email)
{
    try {
        $request = 'SELECT COUNT(*) FROM MEMBRE WHERE Nom_Membre = :name OR Mail_Membre = :email';
        $statement = $db->prepare($request);
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $count = $statement->fetchColumn();
        return $count > 0;
    } catch (PDOException $exception) {
        error_log('User exists check error: ' . $exception->getMessage());
        return false;
    }
}




