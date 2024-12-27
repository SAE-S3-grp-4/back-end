<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('modeleEvent.php');



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

session_start();

if ($requestMethod == "GET") {
    if ($requestRessource == "isLogged") {
        if (isset($_SESSION['Id_User'])) {
            $data = json_encode(['isLogged' => true]);
        } else {
            $data = json_encode(['isLogged' => false]);
        }
    }
}


if ($requestMethod == "GET") {
    if ($requestRessource == "addToCart") {
        if (isset($_SESSION['Id_User'])) {
            if (isset($id) && is_numeric($id)) {
                $userId = $_SESSION['Id_User'];
                $data = addToCart($db, $userId, $id);
            } else {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Invalid product ID']);
                exit;
            }

        }
    }
}

if ($requestMethod == "GET") {
    if ($requestRessource == "delFromCart") {
        if (isset($_SESSION['Id_User'])) {
            if (isset($id) && is_numeric($id)) {
                $userId = $_SESSION['Id_User'];
                $data = delFromCart($db, $userId, $id);
            } else {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Invalid product ID']);
                exit;
            }

        }
    }
}

if ($requestMethod == "GET") {
    if ($requestRessource == "isSubscribed") {
        if (isset($_SESSION['Id_User'])) {
            if (isset($id) && is_numeric($id)) {
                error_log($id);
                error_log($_SESSION['Id_User']);
                $userId = $_SESSION['Id_User'];
                if (isSubscribed($db, $userId, $id)) {
                    $data = true;
                } else {
                    $data = false;
                }
                error_log($data);
            } else {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Invalid product ID']);
                exit;
            }

        }
    }
}

// Send data to the client.
header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
/*if ($data !== false) {
    header('HTTP/1.1 200 OK');
    echo json_encode($data);
} else
    header('HTTP/1.1 400 Bad Request');
exit;*/
header('HTTP/1.1 200 OK');
echo json_encode($data);
exit;