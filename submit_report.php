<?php
session_start();
header('Content-Type: application/json');


include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Error: You must be logged in to submit a report.']);
    exit;
}


$user_id = $_SESSION['user_id'];
$report_type = $_POST['report_type'] ?? ''; 
$item_name = $_POST['item_name'] ?? ''; 
$category = $_POST['category'] ?? '';
$description = $_POST['description'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$location = $_POST['location'] ?? '';


if (empty($report_type) || empty($item_name) || empty($category) || empty($description) || empty($date) || empty($time) || empty($location)) {
    echo json_encode(['success' => false, 'message' => 'Error: All fields are required.']);
    exit;
}


$item_datetime = $date . ' ' . $time . ':00';


$image_path = null;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    
    $upload_dir = 'uploads/';
    
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid('img_', true) . '.' . $file_extension;
    $target_file = $upload_dir . $unique_filename;

    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Error: Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }
    

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $image_path = $target_file; 
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: Failed to move uploaded file.']);
        exit;
    }
}


try {
  

    $sql = "INSERT INTO item_reports 
            (user_id, report_type, item_category, item_name_specific, item_description, image_path, item_datetime, item_location, report_status) 
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, 'pending')"; 
    
    $stmt = $pdo->prepare($sql);
    

    $stmt->execute([
        $user_id,
        $report_type,
        $category,
        $item_name,
        $description,
        $image_path,
        $item_datetime,
        $location
    ]);


    echo json_encode(['success' => true, 'message' => 'Report submitted successfully and is pending approval!']);

} catch (PDOException $e) {
   
    error_log("Submit Report Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
}
?>