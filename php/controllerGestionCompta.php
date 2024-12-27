<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');

require_once('modeleGestionCompta.php');

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

// Request Spreadsheets.
if ($requestRessource == 'spreadsheets') {
    $data = dbRequestspreadsheet($db);
}

if ($requestMethod == "POST") {
    if ($requestRessource == "spreadsheet") {
        $name = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $file = isset($_FILES["file"]["name"]) ? filter_var($_FILES["file"]["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

        if ($name && $file) {
            // Ensure the target directory exists
            $targetDir = "../file/spreadsheet/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Move the uploaded file to the desired directory
            $filePath = $targetDir . basename($file);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
                // Save the spreadsheet data to the database
                $data = dbAddSpreadsheet($db, $name, $file);
            } else {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Failed to upload spreadsheet']);
                exit;
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid spreadsheet data']);
            exit;
        }
    }
}

if ($requestMethod == "DELETE") {
    if ($requestRessource === 'spreadsheet') {
        if (isset($id) && is_numeric($id)) {
            $data = dbDeleteSpreadsheet($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid spreadsheet ID']);
            exit;
        }
    }
}

if ($requestMethod == "GET") {
    if ($requestRessource === 'spreadsheet') {
        if (isset($id) && is_numeric($id)) {
            $data = dbRequestSpreadsheetById($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product ID']);
            exit;
        }
    }
}

if ($requestMethod == "POST") {
    if ($requestRessource == 'spreadsheet-modify') {
        $id = isset($_POST["id"]) ? filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT) : null;
        $name = isset($_POST["nom"]) ? filter_var($_POST["nom"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
        $file = isset($_FILES["file"]["name"]) ? filter_var($_FILES["file"]["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;

        if ($id && $name) {
            if ($file) {
                $targetDir = "../file/spreadsheet/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $filePath = $targetDir . basename($file);
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
                    $data = dbModifySpreadsheet($db, $id, $name, $file);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    echo json_encode(['error' => 'Failed to upload image']);
                    exit;
                }
            } else {
                $data = dbModifySpreadsheet($db, $id, $name, null);
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid product data']);
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