<?php

if (!isset($_GET['group'])) {
    echo json_encode([]);
    exit;
}

$group = $_GET['group'];

// Lire le fichier JSON
$jsonPath = "../json/icals.json"; // Chemin vers le fichier JSON contenant les groupes
$jsonData = file_get_contents($jsonPath);
$groupsData = json_decode($jsonData, true);

// Vérifier la validité du JSON
if (!isset($groupsData['elements'])) {
    echo json_encode([]);
    exit;
}

// Trouver l'URL associée au groupe
$groupUrl = null;
foreach ($groupsData['elements'] as $element) {
    if ($element['classe'] === $group) {
        $groupUrl = $element['url'];
        break;
    }
}

if (!$groupUrl) {
    echo json_encode([]);
    exit;
}

// Télécharger le fichier ICS
$icsData = file_get_contents($groupUrl);

if (!$icsData) {
    echo json_encode([]);
    exit;
}

// Fonction pour analyser les événements
function parseIcs($icsData) {
    $events = [];
    $blocks = explode("BEGIN:VEVENT", $icsData);

    foreach ($blocks as $block) {
        if (strpos($block, "END:VEVENT") !== false) {
            preg_match('/DTSTART:(\d{8})T(\d{6})/', $block, $startMatches);
            preg_match('/SUMMARY:(.+)/', $block, $summaryMatches);

            if ($startMatches && $summaryMatches) {
                $startDate = DateTime::createFromFormat('Ymd', $startMatches[1])->format('Y-m-d');
                $time = substr($startMatches[2], 0, 2) . ':' . substr($startMatches[2], 2, 2);

                $events[] = [
                    'date' => $startDate,
                    'time' => $time,
                    'summary' => $summaryMatches[1],
                ];
            }
        }
    }

    usort($events, function ($a, $b) {
        return strcmp($a['date'] . $a['time'], $b['date'] . $b['time']);
    });

    return $events;
}

// Analyser les événements
echo json_encode(parseIcs($icsData));
?>