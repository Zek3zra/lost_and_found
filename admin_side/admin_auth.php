<?php
// admin_auth.php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$host = 'sql208.infinityfree.com';
$dbname = 'if0_41769205_lost_and_found';
$username = 'if0_41769205';
$password = 'WoIiJKcLvorI';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // On a live site, you'd log this and show a generic error
    die("Database connection failed: " . $e->getMessage());
}
?>