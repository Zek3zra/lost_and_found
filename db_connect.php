<?php
$host = 'localhost';
$dbname = 'lost_and_found';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // On a live site, you'd log this and show a generic error
    die("Database connection failed: " . $e->getMessage());
}
?>