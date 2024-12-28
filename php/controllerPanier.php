<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');

require_once('modelePanier.php');

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

// Request Commandes de produits.
if ($requestRessource == 'commandes-produits') {
    if (isset($_SESSION['Id_User'])) {
        $data = dbRequestProductCommande($db, $_SESSION['Id_User']);
    }
}

// Request Commandes d'événement'.
if ($requestRessource == 'commandes-events') {
    if (isset($_SESSION['Id_User'])) {
        $data = dbRequestEventCommande($db, $_SESSION['Id_User']);
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