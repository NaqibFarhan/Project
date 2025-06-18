<?php
session_start();
include 'includes/db.php';

$email = $_SESSION['email'] ?? null;
$entered_otp = $_POST['otp'] ?? '';

if (!$email || !$entered_otp) {
    echo "<h2 style='color: red;'>❌ Missing email or OTP.</h2>";
    exit;
}

// Get OTP from database
$stmt = $conn->prepare("SELECT otp_code, otp_created_at FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $correct_otp = $row['otp_code'];
    $otp_time = strtotime($row['otp_created_at']);
    $now = time();

    // Optional: Check if OTP expired after 5 minutes
    if ($now - $otp_time > 300) {
        echo "<h2 style='color: red;'>❌ OTP expired. Please request a new one.</h2>";
        exit;
    }

    if ($entered_otp === $correct_otp) {
        $_SESSION['logged_in'] = true;

        // Clear OTP after successful verification
        $clear = $conn->prepare("UPDATE users SET otp_code = NULL, otp_created_at = NULL WHERE email = ?");
        $clear->bind_param("s", $email);
        $clear->execute();

        echo "<h2 style='color: green;'>✅ OTP Verified! Redirecting to your dashboard...</h2>";
        echo "<script>setTimeout(() => window.location.href = 'dashboard.php', 3000);</script>";
        exit;
    } else {
        echo "<h2 style='color: red;'>❌ Invalid OTP. Please try again.</h2>";
    }
} else {
    echo "<h2 style='color: red;'>❌ Email not found.</h2>";
}
?>
