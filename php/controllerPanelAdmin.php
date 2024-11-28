<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('modelePanelAdmin.php');

//session_start();

$db = dbConnect();

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
error_log("Request Method: " . $requestMethod); // Log the request method
error_log("Request Resource: " . $requestRessource); // Log the request resource
error_log("ID received: " . $id); // Log the ID
if ($id == '')
    $id = NULL;
$data = false;


if ($requestRessource === 'students') {
    $data = dbRequestStudents($db);
}


if ($requestMethod == "GET") {
    if ($requestRessource === 'student') {
        if (isset($id) && is_numeric($id)) {
            $data = dbRequestStudentDetails($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid student ID']);
            exit;
        }
    }
}


if ($requestMethod == "DELETE") {
    if ($requestRessource === 'student') {
        if (isset($id) && is_numeric($id)) {
            error_log("Deleting student with ID: " . $id); // Log the deletion
            $success = deleteStudentById($db, $id);
            if ($success) {
                header('HTTP/1.1 200 OK');
                echo json_encode(['success' => true]);
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(['success' => false, 'error' => 'Failed to delete student']);
            }
        } else {
            error_log("Invalid student ID: " . $id); // Log invalid ID
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'error' => 'Invalid student ID']);
        }
        exit;
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