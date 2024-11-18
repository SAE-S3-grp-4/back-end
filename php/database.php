<?php

require_once('constantes.php');

//----------------------------------------------------------------------------
//--- dbConnect --------------------------------------------------------------
//----------------------------------------------------------------------------
// Create the connection to the database.
// \return False on error and the database otherwise.
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

//----------------------------------------------------------------------------
//--- dbRequestPolls ---------------------------------------------------------
//----------------------------------------------------------------------------
// Function to get the polls.
// \param db The connected database.
// \return The list of polls titles.
function dbRequestPhotos($db)
{
  try {
    $request = 'SELECT small FROM photos';
    $statement = $db->prepare($request);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return $result;
}


//----------------------------------------------------------------------------
//--- dbRequestPoll ----------------------------------------------------------
//----------------------------------------------------------------------------
// Function to get a specific poll.
// \param db The connected database.
// \param id The id of the wanted poll.
// \return The poll data.
function dbRequestPhoto($db, $id)
{
  try {
    $request = 'SELECT title, large FROM photos WHERE id=:id';
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

function dbRequestComments($db, $photoId)
{
  try {
    $request = 'SELECT * FROM comments';
    if ($photoId != '')
      $request .= ' WHERE photoId=:photoId';
    $statement = $db->prepare($request);
    if ($photoId != '')
      $statement->bindParam(':photoId', $photoId, PDO::PARAM_STR, 20);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return $result;
}

function dbAddComment($db, $login, $photoId, $text)
{
  try {
    $request = 'INSERT INTO comments(userLogin, photoId, comment) VALUES(:userLogin, :photoId, :comment)';
    $statement = $db->prepare($request);
    $statement->bindParam(':userLogin', $login, PDO::PARAM_STR, 20);
    $statement->bindParam(':photoId', $photoId, PDO::PARAM_INT, 20);
    $statement->bindParam(':comment', $text, PDO::PARAM_STR, 80);
    $statement->execute();
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return true;
}

function dbDeleteComment($db, $id, $login)
{
  error_log($id . " " . $login);
  try {
    $request = 'DELETE FROM comments WHERE id=:id AND userLogin=:userLogin';
    $statement = $db->prepare($request);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->bindParam(':userLogin', $login, PDO::PARAM_STR, 20);
    $statement->execute();
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return true;
}

function dbModifyComment($db, $id, $login, $text)
{
  try {
    $request = 'UPDATE comments SET comment=:comment WHERE id=:id AND userLogin=:userLogin';
    $statement = $db->prepare($request);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->bindParam(':userLogin', $login, PDO::PARAM_STR, 20);
    $statement->bindParam(':comment', $text, PDO::PARAM_STR, 80);
    $statement->execute();
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return true;
}

function dbRequestUser($db)
{
  try {
    $request = 'SELECT * FROM MEMBRE';
    $statement = $db->prepare($request);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $exception) {
    error_log('Request error: ' . $exception->getMessage());
    return false;
  }
  return $result;
}