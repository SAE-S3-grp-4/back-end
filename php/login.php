<?php
require_once('database.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    $db = dbConnect();
    if ($db) {
        $user = dbLoginUser($db, $username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
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
