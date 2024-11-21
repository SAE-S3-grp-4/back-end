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

function dbAddProduct($db, $nom, $desc, $img, $prix, $stock)
{
  try {
    $request = 'INSERT INTO PRODUIT(Nom_Produit, Description_Produit, Img_Produit, Prix_Produit, Stock_Produit) VALUES(:Nom_Produit, :Description_Produit, :Img_Produit, :Prix_Produit, :Stock_Produit)';
    $statement = $db->prepare($request);
    $statement->bindParam(':Nom_Produit', $nom, PDO::PARAM_STR, 50);
    $statement->bindParam(':Description_Produit', $desc, PDO::PARAM_STR, 200);
    $statement->bindParam(':Img_Produit', $img, PDO::PARAM_STR, 200);
    $statement->bindParam(':Prix_Produit', $prix, PDO::PARAM_INT, 10);
    $statement->bindParam(':Stock_Produit', $stock, PDO::PARAM_INT, 10);
    //var_dump($statement);
    $statement->execute();
  } catch (PDOException $exception) {
    error_log('Request error:' . $exception->getMessage());
    return false;
  }
  return true;
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

function dbDeleteProduct($db, $id)
{
  try {
    $request = 'DELETE FROM produit WHERE Id_Produit = :id';
    $statement = $db->prepare($request);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
  } catch (PDOException $exception) {
    error_log('Request error :' . $exception->getMessage());
  }
}

function dbDeleteEvent($db, $id)
{
    try {
        $request = 'DELETE FROM EVENEMENT WHERE Id_Event = :id';
        $statement = $db->prepare($request);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log('Request error: ' . $exception->getMessage());
        return false;
    }
    return true;
}