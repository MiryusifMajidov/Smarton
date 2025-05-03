<?php
$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'smarton';

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die('Verilənlər bazasına qoşulma xətası: ' . $mysqli->connect_error);
}
