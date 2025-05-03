<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'smarton';

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die('DB bağlantı xətası: ' . $mysqli->connect_error);
}

if (isset($_GET['id'])) {
    $productId = (int) $_GET['id'];
    $query = "DELETE FROM products WHERE id = $productId";

    if ($mysqli->query($query)) {
        header('Location: product.php');
        exit;
    } else {
        echo "Silinmə xətası: " . $mysqli->error;
    }
}
?>
