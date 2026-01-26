<?php
include __DIR__ . "/definitions.php";
include __DIR__ . "/init_functions.php";
error_reporting(E_ERROR);
ini_set('display_errors', intval($_ENV["DISPLAY_ERRORS"]));
ini_set('expose_php', 'off');
$db_host = $_ENV["DB_HOST"];
$db_username = $_ENV["DB_USERNAME"];
$db_password = $_ENV["DB_PASSWORD"];
$db_database = $_ENV["DB_DATABASE"];
$encryption_password = $_ENV["ENCRYPTION_PASSWORD"];
$api_version = 1;
$res_version = "24";
?>