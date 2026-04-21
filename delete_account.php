<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit;
}


include 'db_connect.php'; 

$user_id = $_SESSION['user_id'];


try {
    $pdo->beginTransaction();

   
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

  
    $pdo->commit();

    
    session_unset();
    session_destroy();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    
    $pdo->rollBack();
    
   
    error_log('Delete account error for user ' . $user_id . ': ' . $e.getMessage()); 
    
   
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Could not delete account.']);
}

exit;
?>

