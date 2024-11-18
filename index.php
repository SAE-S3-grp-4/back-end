<?php

require 'php/database.php';
$db = dbConnect();
$data = dbRequestUser($db);

var_dump($data);
?>