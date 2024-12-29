<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');

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

if ($requestMethod == "GET") {
    if ($requestRessource === 'student-registrations') {
        if (isset($id) && is_numeric($id)) {
            $data = dbRequestStudentRegistrations($db, $id);
            error_log("Requesting student registrations for ID: " . $id); // Log the request
            error_log("Data: " . print_r($data, true)); // Log the data
        } else {
            error_log("Invalid student ID: " . $id); // Log invalid ID
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'error' => 'Invalid student ID']);
            exit;
        }

    }
}

if ($requestMethod == "PUT" && $requestRessource === 'student' && isset($id) && is_numeric($id)) {
    // PHP ne remplit pas $_POST pour les requêtes PUT, on doit donc récupérer les données de php://input manuellement
    parse_str(file_get_contents('php://input'), $input);

    // Log des données reçues pour débogage
    error_log("Parsed Input (URL-encoded): " . print_r($input, true));

    // Vérification de l'entrée
    if (!isset($input['role']) || empty(trim($input['role']))) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Invalid or missing role']);
        error_log("Invalid or missing role in input: " . print_r($input, true));
        exit;
    }

    $role = trim($input['role']);

    // Mettre à jour le rôle dans la base de données
    $success = updateStudentRole($db, $id, $role);
    if ($success) {
        header('HTTP/1.1 200 OK');
        echo json_encode(['success' => true]);
        error_log("Role updated successfully for ID: $id, Role: $role");
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'error' => 'Database update failed']);
        error_log("Failed to update role for ID: $id");
    }
    exit;
}

// Send data to the client.
header('Content-Type: application/json; charset=utf-8');
header('Cache-control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
//if ($data !== false) {
//    header('HTTP/1.1 200 OK');
//    echo json_encode($data);
//} else
//    header('HTTP/1.1 400 Bad Request');
//exit;
header('HTTP/1.1 200 OK');
echo json_encode($data);
exit;