<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('modeleGestionBoutique.php');

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

if ($requestMethod == "POST") {
    if ($requestRessource == 'member-id') {

        if (isset($_SESSION['Id_User']) && $_SESSION['Id_User']) {
            $data = ['Id_User' => $_SESSION['Id_User']];
        }
        else {
            $data = ['Id_User' => NULL];
        }
    }
}

if ($requestMethod == "POST") {
    if ($requestRessource == 'deconnexion') {
        $_SESSION['Id_User'] = NULL;
        $_SESSION['user'] = NULL;
        $_SESSION['is_admin'] = NULL;
        if (session_destroy()) {
            $data = ['success' => true];
        } else {
            $data = ['success' => false];
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
