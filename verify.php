<?php
session_start();


include 'db_connect.php';

$status = 'error';
$message = 'Invalid or missing verification token.';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
      
        $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
           
            $update_stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
            $update_stmt->execute([$user['id']]);

            $status = 'success';
            $message = 'Your email has been successfully verified! You can now log in to your account.';
        } else {
           
            $status = 'warning';
            $message = 'This link is invalid or your account has already been verified.';
        }

    } catch (PDOException $e) {
        $status = 'error';
        $message = 'A database error occurred while verifying your account.';
        error_log("Verification error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrieve TUPV | Email Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1E3A8A; 
            --primary-blue-hover: #1E40AF;
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --success-green: #10b981;
            --warning-amber: #F59E0B;
            --error-red: #ef4444;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .verify-card {
            background-color: var(--bg-surface);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 24px;
            font-size: 2rem;
        }

        .icon-circle.success { background-color: rgba(16, 185, 129, 0.1); color: var(--success-green); }
        .icon-circle.warning { background-color: rgba(245, 158, 11, 0.1); color: var(--warning-amber); }
        .icon-circle.error { background-color: rgba(239, 68, 68, 0.1); color: var(--error-red); }

        h2 {
            margin: 0 0 12px;
            color: var(--text-primary);
            font-size: 1.5rem;
        }

        p {
            color: var(--text-secondary);
            margin: 0 0 32px;
            line-height: 1.6;
        }

        .login-btn {
            display: inline-block;
            background-color: var(--primary-blue);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .login-btn:hover { background-color: var(--primary-blue-hover); transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="verify-card">
        <?php if ($status === 'success'): ?>
            <div class="icon-circle success"><i class="fa-solid fa-check"></i></div>
            <h2>Verification Successful!</h2>
            <p><?php echo $message; ?></p>
            <a href="login.html" class="login-btn">Go to Login</a>
            
        <?php elseif ($status === 'warning'): ?>
            <div class="icon-circle warning"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h2>Link Expired</h2>
            <p><?php echo $message; ?></p>
            <a href="login.html" class="login-btn">Go to Login</a>
            
        <?php else: ?>
            <div class="icon-circle error"><i class="fa-solid fa-xmark"></i></div>
            <h2>Verification Failed</h2>
            <p><?php echo $message; ?></p>
            <a href="register.html" class="login-btn">Back to Registration</a>
        <?php endif; ?>
    </div>

</body>
</html>