<?php

require_once('modelePanelAdmin.php');
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
if ($id == '')
    $id = NULL;
$data = false;


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
    if ($requestRessource === 'students') {
        $data = dbRequestStudents($db);
    }
}


if ($requestMethod == "DELETE") {
    if ($requestRessource === 'student') {
        if (isset($id) && is_numeric($id)) {
            $data = deleteStudentById($db, $id);
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid student ID']);
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