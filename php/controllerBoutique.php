<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('modeleBoutique.php');

session_start();

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'isLoggedIn') !== false) {
    header('Content-Type: application/json');
    echo json_encode(['loggedIn' => isset($_SESSION['user_id'])]);
    exit;
}

// Handle adding product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], 'addToCart') !== false) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $productId = filter_input(INPUT_POST, 'productId', FILTER_SANITIZE_NUMBER_INT);
        if ($productId) {
            $result = addToCart($userId, $productId);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product ID']);
        }
    } else {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'User not logged in']);
    }
    exit;
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