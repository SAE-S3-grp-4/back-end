<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['Id_User']) && $_SESSION['Id_User']) {
    echo json_encode(['is_connected' => true]);
} else {
    echo json_encode(['is_connected' => false]);
}