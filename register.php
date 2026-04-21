<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'lost_and_found';
$username = 'root';
$password = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}


$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$email = trim($_POST['email'] ?? '');
$contactNumber = trim($_POST['contact_number'] ?? '');
$courseSection = trim($_POST['course_section'] ?? '');
$address = trim($_POST['address'] ?? '');
$plainPassword = $_POST['password'] ?? '';


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

   
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, contact_number, course_section, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $contactNumber, $courseSection, $address]);

    echo json_encode(['success' => true, 'message' => 'Registration successful!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again later.']);
}
?>