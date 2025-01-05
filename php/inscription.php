<?php
ini_set('log_errors', 'On');
ini_set('error_log', 'php_error.log');
error_log('Ceci est un test de journalisation.');
require_once('database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
    $surname = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
    $pseudo = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $confirmPassword = filter_var($_POST['confirm-password'], FILTER_SANITIZE_STRING);
    $group = filter_var($_POST['group'], FILTER_SANITIZE_STRING);

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'error' => 'Les mots de passe ne correspondent pas']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $db = dbConnect();
    if ($db) {
        if (dbUserExists($db, $pseudo, $email)) {
            echo json_encode(['success' => false, 'error' => 'Le nom d\'utilisateur ou l\'email est déjà utilisé']);
        } else {
            $result = dbRegisterUser($db, $name, $surname, $pseudo, $email, $hashedPassword, $group);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Échec de l\'inscription']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide']);
}
