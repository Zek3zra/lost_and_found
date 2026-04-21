<?php
session_start();
require 'db_connect.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $contact_number = trim($_POST['contact_number']);
    $course_section = trim($_POST['course_section']);
    $address = trim($_POST['address']);

    try {
        
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, contact_number = ?, course_section = ?, address = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $contact_number, $course_section, $address, $user_id]);

        
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;

      
        header("Location: profile.php?update=success");
        exit;

    } catch (PDOException $e) {
     
        error_log("Profile Update Error: " . $e->getMessage());
        header("Location: profile.php?update=error");
        exit;
    }
} else {
  
    header("Location: profile.php");
    exit;
}
?>