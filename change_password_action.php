<?php
session_start();
require 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    
    if ($new_password !== $confirm_password) {
        header("Location: profile.php?pwd_update=mismatch");
        exit;
    }

    try {
     
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($user && password_verify($current_password, $user['password'])) {
            
            
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->execute([$hashed_new_password, $user_id]);

            header("Location: profile.php?pwd_update=success");
            exit;

        } else {
           
            header("Location: profile.php?pwd_update=incorrect");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Password Update Error: " . $e->getMessage());
        header("Location: profile.php?pwd_update=error");
        exit;
    }
} else {
    header("Location: profile.php");
    exit;
}
?>