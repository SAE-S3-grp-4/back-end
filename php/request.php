<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Request Produits.
if ($requestRessource == 'produits') {
  $data = dbRequestProduct($db);
}

if ($requestRessource == 'events') {
  $data = dbRequestEvent($db);
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
    if ($requestRessource == "produit") {
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_STRING) : null;
        $description = isset($_POST["description"]) ? filter_var($_POST["description"], FILTER_SANITIZE_STRING) : null;
        $prix = isset($_POST["prix"]) ? filter_var($_POST["prix"], FILTER_SANITIZE_NUMBER_INT) : null;
        $stock = isset($_POST["stock"]) ? filter_var($_POST["stock"], FILTER_SANITIZE_NUMBER_INT) : null;
        $img = isset($_POST["image"]) ? filter_var($_POST["image"], FILTER_SANITIZE_STRING) : null;

        $data = dbAddProduct($db, $nom, $description, $img, $prix, $stock);
    }
}
if ($requestMethod == "POST") {
    if ($requestRessource == 'event') {
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $description = isset($_POST["description"]) ? filter_var($_POST["description"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $prix = isset($_POST["prix"]) ? filter_var($_POST["prix"], FILTER_SANITIZE_NUMBER_INT) : null;
        $date = isset($_POST["date"]) ? filter_var($_POST["date"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $nbPlace = isset($_POST["nbPlace"]) ? filter_var($_POST["nbPlace"], FILTER_SANITIZE_NUMBER_INT) : null;
        $dateFinInscription = isset($_POST["dateFinInscription"]) ? filter_var($_POST["dateFinInscription"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $data = dbAddEvent($db, $nom, $description, $date, $prix, $nbPlace, $dateFinInscription);
    }
}
if ($requestMethod == "DELETE") {
    if ($requestRessource == 'event') {
        if (isset($id)) {
            $data = dbDeleteEvent($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid event ID']);
            exit;
        }
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
  if ($requestRessource === 'produit') {
    $id = $_GET["id"];
    // casque
    $data = dbDeleteProduct($db, $id);
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