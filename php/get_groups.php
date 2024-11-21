<?php
$json = file_get_contents("../json/icals.json");

$data = json_decode($json, true);
$options = "";

foreach ($data['elements'] as $element) {
    $options .= '<option value="' . $element['url'] . '">' . $element['classe'] . '</option>';
}

echo $options;