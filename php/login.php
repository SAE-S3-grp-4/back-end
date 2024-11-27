<?php
require_once('database.php');

ini_set('log_errors', 'On');
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log('Ceci est un test de journalisation.');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    error_log('Login attempt with username: ' . $username);
    $db = dbConnect();
    if ($db) {
        $user = dbLoginUser($db, $username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            $_SESSION['Id_User'] = dbRequestUserId($db, $username);
            error_log('User ID from session: ' . $_SESSION['Id_User']);
            $_SESSION['is_admin'] = ($user['Id_Role'] == 3);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Nom d\'utilisateur ou mot de passe incorrect']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide']);
}
