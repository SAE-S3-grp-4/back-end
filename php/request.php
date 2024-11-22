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
    if ($requestRessource === 'students') {
        $data = dbRequestStudents($db);
    }
}

if ($requestMethod == "POST") {
    if ($requestRessource == "comments") {
        $textToPost = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
        $data = dbAddComment($db, $_POST["userLogin"], $_POST["photoId"], $textToPost);
    }
}
if ($requestMethod == "POST") {
    if ($requestRessource == "produit") {
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $description = isset($_POST["description"]) ? filter_var($_POST["description"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $prix = isset($_POST["prix"]) ? filter_var($_POST["prix"], FILTER_SANITIZE_NUMBER_INT) : null;
        $stock = isset($_POST["stock"]) ? filter_var($_POST["stock"], FILTER_SANITIZE_NUMBER_INT) : null;
        $img = isset($_FILES["image"]["name"]) ? filter_var($_FILES["image"]["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

        if ($nom && $description && $prix && $stock && $img) {
            // Ensure the target directory exists
            $targetDir = "img/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Move the uploaded file to the desired directory
            $imgPath = $targetDir . basename($img);
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $imgPath)) {
                $data = dbAddProduct($db, $nom, $description, $img, $prix, $stock);
            } else {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Failed to upload image']);
                exit;
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product data']);
            exit;
        }
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
}
if ($requestMethod == "DELETE") {
    if ($requestRessource === 'produit') {
        if (isset($id) && is_numeric($id)) {
            $data = dbDeleteProduct($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product ID']);
            exit;
        }
    }
}

if ($requestMethod == "GET") {
    if ($requestRessource === 'produit') {
        if (isset($id) && is_numeric($id)) {
            $data = dbRequestProductById($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product ID']);
            exit;
        }
    }
}

if ($requestMethod == "GET") {
    if ($requestRessource === 'event') {
        if (isset($id) && is_numeric($id)) {
            $data = dbRequestEventById($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid event ID']);
            exit;
        }
    }
}

if ($requestMethod == "POST") {
    if ($requestRessource == 'produit-modify') {
        $id = isset($_POST["id"]) ? filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT) : null;
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $description = isset($_POST["description"]) ? filter_var($_POST["description"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $prix = isset($_POST["prix"]) ? filter_var($_POST["prix"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
        $stock = isset($_POST["stock"]) ? filter_var($_POST["stock"], FILTER_SANITIZE_NUMBER_INT) : null;
        $img = isset($_FILES["image"]["name"]) ? filter_var($_FILES["image"]["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

        if ($id && $nom && $description && $prix && $stock) {
            if ($img) {
                $targetDir = "img/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $imgPath = $targetDir . basename($img);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $imgPath)) {
                    $data = dbModifyProduct($db, $id, $nom, $description, $img, $prix, $stock);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'Failed to upload image']);
                    exit;
                }
            } else {
                $data = dbModifyProduct($db, $id, $nom, $description, null, $prix, $stock);
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product data']);
            exit;
        }
    }
}

if ($requestMethod == "POST") {
    if ($requestRessource == 'event-modify') {
        $id = isset($_POST["id"]) ? filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT) : null;
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $description = isset($_POST["description"]) ? filter_var($_POST["description"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $date = isset($_POST["date"]) ? filter_var($_POST["date"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $prix = isset($_POST["prix"]) ? filter_var($_POST["prix"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
        $nbPlace = isset($_POST["nbPlace"]) ? filter_var($_POST["nbPlace"], FILTER_SANITIZE_NUMBER_INT) : null;
        $dateFinInscription = isset($_POST["dateFinInscription"]) ? filter_var($_POST["dateFinInscription"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

        if ($id && $nom && $description && $date && $prix && $nbPlace && $dateFinInscription) {
            $data = dbModifyEvent($db, $id, $nom, $description, $date, $prix, $nbPlace, $dateFinInscription);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid event data']);
            exit;
        }
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