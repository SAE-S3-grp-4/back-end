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
    $request = 'INSERT INTO PRODUIT(Nom_Produit, Description_Produit, Img_Produit, Prix_Produit, Stock_Produit) VALUES(:nom, :desc, :img, :prix, :stock)';
    $statement = $db->prepare($request);
    $statement->bindParam(':nom', $nom, PDO::PARAM_STR, 50);
    $statement->bindParam(':desc', $desc, PDO::PARAM_STR, 200);
    $statement->bindParam(':img', $img, PDO::PARAM_STR, 200);
    $statement->bindParam(':prix', $prix, PDO::PARAM_INT, 10);
    $statement->bindParam(':stock', $stock, PDO::PARAM_INT, 10);
    $statement->execute();
  } catch (PDOException $exception) {
    error_log('Request error:' . $exception->getMessage());
    return false;
  }
}


