<?php
$host = 'sql208.infinityfree.com';
$dbname = 'if0_41769205_lost_and_found'; // <-- This one gets the full name
$username = 'if0_41769205';              // <-- This one is JUST your account number
$password = 'WoIiJKcLvorI';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // On a live site, you'd log this and show a generic error
    die("Database connection failed: " . $e->getMessage());
}
?>