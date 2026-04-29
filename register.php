<?php
session_start();
// Prevent PHP from outputting warnings that break JSON
error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json');

// 1. Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$host = 'localhost';
$dbname = 'lost_and_found';
$username = 'root';
$password = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get and sanitize input
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$email = trim($_POST['email'] ?? '');
$contactNumber = trim($_POST['contact_number'] ?? '');
$courseSection = trim($_POST['course_section'] ?? '');
$address = trim($_POST['address'] ?? '');
$plainPassword = $_POST['password'] ?? '';

// Validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($plainPassword) || empty($contactNumber) || empty($courseSection) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

if (strlen($plainPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if email OR (first_name + last_name) already exists
    $stmt = $pdo->prepare("
        SELECT id FROM users 
        WHERE email = ? 
        OR (first_name = ? AND last_name = ?)
    ");
    $stmt->execute([$email, $firstName, $lastName]);

    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'An account with this email or name already exists.']);
        exit;
    }

    // 2. Hash password and generate email token
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    $verification_token = bin2hex(random_bytes(32));

    // 3. Insert ALL fields, including the token and setting is_verified to 0
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, contact_number, course_section, address, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $contactNumber, $courseSection, $address, $verification_token]);

    // 4. Send the verification email using PHPMailer
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    
    $mail->Username   = 'tupvlostandfound@gmail.com'; 
    $mail->Password   = 'tlhesjaxjaiteygq'; // <--- SPACES REMOVED HERE
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // --- XAMPP LOCALHOST SSL BYPASS ---
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    // ----------------------------------

    $mail->setFrom('tupvlostandfound@gmail.com', 'Retrieve TUPV Admin'); 
    $mail->addAddress($email, $firstName . ' ' . $lastName);

    $verify_link = "http://localhost/lost_and_found/verify.php?token=" . $verification_token;

    $mail->isHTML(true);
    $mail->Subject = 'Verify Your Retrieve TUPV Account';
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
            <h2 style='color: #1E3A8A;'>Welcome to Retrieve TUPV, $firstName!</h2>
            <p style='color: #475569;'>Thank you for registering. Please click the button below to verify your email address and activate your account.</p>
            <br>
            <div style='text-align: center;'>
                <a href='$verify_link' style='background-color: #1E3A8A; color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>Verify Email Address</a>
            </div>
            <br><br>
            <p style='color: #475569; font-size: 0.9em;'>If the button doesn't work, copy and paste this link into your browser:</p>
            <p style='color: #1E3A8A; font-size: 0.85em; word-break: break-all;'>$verify_link</p>
        </div>
    ";

    $mail->send();

    // 5. Send JSON success
    echo json_encode([
        'success' => true, 
        'message' => 'Sign-up complete! Please check your email to verify your account.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>