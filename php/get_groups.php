<?php
// Lire le fichier JSON
$jsonPath = "../json/icals.json"; // Chemin vers le fichier JSON contenant les groupes
$jsonData = file_get_contents($jsonPath);
$groupsData = json_decode($jsonData, true);

// Vérifier la validité du JSON
if (!isset($groupsData['elements'])) {
    echo "<option value=''>Aucun groupe trouvé</option>";
    exit;
}

// Générer les options HTML à partir des données JSON
$options = "";
foreach ($groupsData['elements'] as $group) {
    $classe = htmlspecialchars($group['classe']);
    $options .= "<option value=\"$classe\">$classe</option>";
}

echo $options;
?>