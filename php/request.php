<?php

require_once('database.php');

// Database connexion.
$db = dbConnect();
$login = 'cir2';

if (!$db) {
  header('HTTP/1.1 503 Service Unavailable');
  exit;
}

// Check the request.
$requestMethod = $_SERVER['REQUEST_METHOD'];
$request = substr($_SERVER['PATH_INFO'], 1);
$request = explode('/', $request);
$requestRessource = array_shift($request);

// Check the id associated to the request.
$id = array_shift($request);
if ($id == '')
  $id = NULL;
$data = false;


if ($requestMethod == "POST") {
  if ($requestRessource == "comments") {
    $textToPost = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
    $data = dbAddComment($db, $_POST["userLogin"], $_POST["photoId"], $textToPost);
  }
}


// Send data to the client.
header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
if ($data !== false) {
  header('HTTP/1.1 200 OK');
  echo json_encode($data);
} else
  header('HTTP/1.1 400 Bad Request');
exit;
