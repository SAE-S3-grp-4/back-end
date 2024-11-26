<?php

date_default_timezone_set('Europe/Paris'); // Ajuste selon ton emplacement

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
            preg_match('/DTEND:(\d{8})T(\d{6})/', $block, $endMatches);
            preg_match('/SUMMARY:(.+)/', $block, $summaryMatches);

            if ($startMatches && $endMatches && $summaryMatches) {

                $dateTimeUtc = DateTime::createFromFormat('Ymd\THis', $startMatches[1] . 'T' . $startMatches[2], new DateTimeZone('UTC'));
                $dateTimeLocal = $dateTimeUtc->setTimezone(new DateTimeZone('Europe/Paris')); // Ajuste pour ton fuseau horaire

                $startDate = $dateTimeLocal->format('Y-m-d');
                $startTime = $dateTimeLocal->format('H:i');

                // Récupérer la date et l'heure de fin
                $endTime = substr($endMatches[2], 0, 2) . ':' . substr($endMatches[2], 2, 2);

                // Calcul de la durée en heures
                $startDateTime = DateTime::createFromFormat('Ymd His', $startMatches[1] . ' ' . $startMatches[2]);
                $endDateTime = DateTime::createFromFormat('Ymd His', $endMatches[1] . ' ' . $endMatches[2]);
                $interval = $startDateTime->diff($endDateTime);
                $duration = $interval->h + ($interval->i / 60); // Convertir minutes en fraction d'heures

                // Ajouter l'événement à la liste
                $events[] = [
                    'date' => $startDate,
                    'time' => $startTime,
                    'summary' => $summaryMatches[1],
                    'duration' => $duration, // Durée en heures
                ];
            }
        }
    }

    // Trier les événements par date et heure
    usort($events, function ($a, $b) {
        return strcmp($a['date'] . $a['time'], $b['date'] . $b['time']);
    });

    return $events;
}

// Analyser les événements
echo json_encode(parseIcs($icsData));
?>