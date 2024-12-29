<?php
session_start();
ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');
require_once('constantes.php');
require_once('database.php');
header('Content-Type: application/json');


$db = dbConnect();

if (!isset($_SESSION['Id_User'])) {
    echo json_encode(['is_admin' => false]);
    exit();
}
error_log($_SESSION['Id_User']);
error_log(dbRequestUserRole($db, $_SESSION['Id_User']));

if (dbRequestUserRole($db, $_SESSION['Id_User'])==3) {

    echo json_encode(['is_admin' => true]);
} else {
    echo json_encode(['is_admin' => false]);
}

