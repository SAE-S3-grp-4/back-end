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

// Request Photos.
if ($requestRessource == 'photos') {
  if ($id != NULL)
    $data = dbRequestPhoto($db, intval($id));
  else
    $data = dbRequestPhotos($db);
}

if ($requestMethod == "POST") {
  if ($requestRessource == "comments") {
    $textToPost = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
    $data = dbAddComment($db, $_POST["userLogin"], $_POST["photoId"], $textToPost);
  }
}


if ($requestMethod == "GET") {
  if ($requestRessource == "comments") {
    if (isset($_GET["photoId"])) {
      $data = dbRequestComments($db, $_GET["photoId"]);
    }
  }
}

if ($requestMethod == "POST") {
  if ($requestRessource == "comments") {
    $textToPost = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
    $data = dbAddComment($db, $_POST["userLogin"], $_POST["photoId"], $textToPost);
  }

}

if ($requestMethod == "PUT") {
  if ($requestRessource == "comments") {
    $put_data = file_get_contents("php://input");
    parse_str($put_data, $post_vars);
    $textToPost = filter_var($post_vars["comment"], FILTER_SANITIZE_STRING);
    $data = dbModifyComment($db, $id, $login, $textToPost);
  }
}

if ($requestMethod == "DELETE") {
  if ($requestRessource == "comments") {
    $data = dbDeleteComment($db, $id, $_GET["userLogin"]);
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