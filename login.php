<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'lost_and_found';
$username = 'root';
$password = '';

$admin_email = 'lostandfoundadmin@gmail.com';
$admin_pass = 'admin12345'; // The plain-text password

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

// --- MODIFICATION: Hardcoded Admin Check ---
// Check if the credentials match the special admin account
if ($email === $admin_email && $plainPassword === $admin_pass) {
    
    // Manually start the admin session
    $_SESSION['user_id'] = 'admin_001'; // Assign a special ID
    $_SESSION['user_email'] = $admin_email;
    $_SESSION['user_name'] = 'Site Administrator';
    $_SESSION['role'] = 'admin'; // This is the most important part
    $_SESSION['user_pfp'] = null; // Admin has no PFP for now
    
    // Send a success response with a redirect to the admin panel
    echo json_encode([
        'success' => true, 
        'message' => 'Admin login successful! Redirecting...',
        'redirect' => 'admin_side/overview.php' // <-- MODIFIED REDIRECT
    ]);
    exit; // Stop the script here
}
// --- END OF MODIFICATION ---


// --- Regular User Login Logic (if not admin) ---
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check the database for a regular user
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, profile_picture_path, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($plainPassword, $user['password'])) {
        
        // --- MODIFICATION: Check if a DB user is ALSO an admin ---
        if ($user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role'] = 'admin';
            $_SESSION['user_pfp'] = $user['profile_picture_path'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Admin login successful! Redirecting...',
                'redirect' => 'admin_side/overview.php' // <-- Admin redirect
            ]);
            exit;
        }

        // --- Standard User Login ---
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['role'] = $user['role']; // Will be 'user'
        $_SESSION['user_pfp'] = $user['profile_picture_path'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful! Redirecting...',
            'redirect' => 'homepage.php' // <-- Standard user redirect
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.']);
}
?>