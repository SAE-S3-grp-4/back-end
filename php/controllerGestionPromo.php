<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('modeleGestionPromo.php');

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

if ($requestRessource == 'promos') {
    $data = dbRequestPromo($db);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($requestRessource == 'promo') {
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $pourcentage = isset($_POST["pourcentage"]) ? filter_var($_POST["pourcentage"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;

        if ($nom && $pourcentage) {
            $result = dbAddPromo($db, $nom, $pourcentage);
            if ($result['success']) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $result['error']]);
                exit;
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid promo data']);
            exit;
        }
    }
}

if ($requestMethod == "POST") {
    if ($requestRessource == 'promo-modify') {
        $id = isset($_POST["id"]) ? filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT) : null;
        $nom = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $pourcentage = isset($_POST["pourcentage"]) ? filter_var($_POST["pourcentage"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;

        if ($id && $nom && $pourcentage) {
            $data = dbModifyPromo($db, $id, $nom, $pourcentage);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid promo data']);
            exit;
        }
    }
}

if ($requestMethod == "DELETE") {
    if ($requestRessource == 'promo') {
        if (isset($id) && is_numeric($id)) {
            $data = dbDeletePromo($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid promo ID']);
            exit;
        }
    }
}

if ($requestMethod == "GET") {
    if ($requestRessource === 'promo') {
        if (isset($id) && is_numeric($id)) {
            $data = dbRequestPromoById($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid promo ID']);
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