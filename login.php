<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'lost_and_found';
$username = 'root';
$password = '';

$admin_email = 'lostandfoundadmin@gmail.com';
$admin_pass = 'admin12345'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$plainPassword = $_POST['password'] ?? '';

if (empty($email) || empty($plainPassword)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}


if ($email === $admin_email && $plainPassword === $admin_pass) {
    
  
    $_SESSION['user_id'] = 'admin_001';
    $_SESSION['user_email'] = $admin_email;
    $_SESSION['user_name'] = 'Site Administrator';
    $_SESSION['role'] = 'admin'; 
    $_SESSION['user_pfp'] = null; 
    
   
    echo json_encode([
        'success' => true, 
        'message' => 'Admin login successful! Redirecting...',
        'redirect' => 'admin_side/overview.php' 
    ]);
    exit; 
}




try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, profile_picture_path, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($plainPassword, $user['password'])) {
        
       
        if ($user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role'] = 'admin';
            $_SESSION['user_pfp'] = $user['profile_picture_path'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Admin login successful! Redirecting...',
                'redirect' => 'admin_side/overview.php' 
            ]);
            exit;
        }

  
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['role'] = $user['role']; 
        $_SESSION['user_pfp'] = $user['profile_picture_path'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful! Redirecting...',
            'redirect' => 'homepage.php'
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.']);
}
?>