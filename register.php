<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("includes/db.php");
session_start();

$message = "";

// Only process after form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        $message = "❌ All fields are required.";
    } else {
        // Check if username already exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "❌ Username already exists.";
        } else {
            // Insert new user with hashed password
            $hashed = md5($password); // ⚠️ Use password_hash() in production

            $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed, $email);
            if ($stmt->execute()) {
                $_SESSION['email'] = $email; // for OTP
                header("Location: send_otp.php");
                exit;
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h2>Register</h2>
        <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
        <form method="POST">
            <input type="text" name="username" required placeholder="Nickname">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>

