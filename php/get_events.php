<?php

if (!isset($_GET['group']) || !isset($_GET['date'])) {
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

$groupUrl = $_GET['group'];
$date = $_GET['date'];

// Vérifiez si l'URL du groupe est correcte
if (!filter_var($groupUrl, FILTER_VALIDATE_URL)) {
    echo json_encode(['error' => 'URL du groupe invalide']);
    exit;
}

// Essayez de télécharger le fichier ICS
$icsData = @file_get_contents($groupUrl);

if (!$icsData) {
    echo json_encode(['error' => 'Impossible de télécharger le fichier ICS']);
    exit;
}

// Parser les événements dans l'ICS
function parseIcs($icsData, $targetDate) {
    $events = [];
    $blocks = explode("BEGIN:VEVENT", $icsData);

    foreach ($blocks as $block) {
        if (strpos($block, "END:VEVENT") !== false) {
            preg_match('/DTSTART:(\d{8})T(\d{6})/', $block, $startMatches);
            preg_match('/SUMMARY:(.+)/', $block, $summaryMatches);

            if ($startMatches && $summaryMatches) {
                $startDate = DateTime::createFromFormat('Ymd', $startMatches[1])->format('Y-m-d');

                if ($startDate === $targetDate) {
                    $events[] = [
                        'time' => substr($startMatches[2], 0, 2) . ':' . substr($startMatches[2], 2, 2),
                        'summary' => $summaryMatches[1],
                    ];
                }
            }
        }
    }

    return $events;
}

echo json_encode(parseIcs($icsData, $date));
?>