<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("includes/db.php");

$error = "";
$success = "";

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = md5(trim($_POST["password"])); // In production, use password_hash

    // ✅ Fetch user with matching username and password
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ If user exists, get their email and redirect
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['email'] = $user['email'];     // ✅ This is the user's email
        $_SESSION['username'] = $user['username']; // Optional: store username
        header("Location: send_otp.php");
        exit;
    } else {
        $error = "❌ Invalid username or password.";
    }
}

// Show success message after registration
if (isset($_GET['registered'])) {
    $success = "✅ Registration successful. Please log in.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page" style="background: url('images/kovokesyen-e1603864966925.jpg') no-repeat center center fixed; background-size: cover;">
<div class="login-box">
    <div class="logo-container">
        <img src="images/logo.png" alt="System Logo" class="login-logo">
        <h2 class="system-name"><strong>Student Attendance Management System</strong></h2>
    </div>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php elseif ($success): ?>
        <p class="success-msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- ✅ Login form (no email field here) -->
    <form method="POST">
        <input type="text" name="username" required placeholder="Nickname">
        <input type="password" name="password" required placeholder="Password">
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>
