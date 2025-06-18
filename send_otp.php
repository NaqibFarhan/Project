<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require 'includes/db.php';

$email = $_SESSION['email'] ?? null;

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Email not found or invalid.";
    exit;
}

$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$now = date("Y-m-d H:i:s");

// Save OTP in DB
$stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_created_at = ? WHERE email = ?");
$stmt->bind_param("sss", $otp, $now, $email);
$stmt->execute();

// Send Email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'naqibfarhan.nf@gmail.com';      // ✅ your Gmail
    $mail->Password   = 'buxijvyjnyztmhjp';               // ✅ your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('naqibfarhan.nf@gmail.com', 'SmartTrack OTP');  // ✅ must match your Gmail
    $mail->addAddress($email);  // ✅ this is the recipient: the user's email
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body    = "<h3>Your OTP is <strong>$otp</strong></h3>";

    $mail->send();
    header("Location: otp_form.php");
    exit;
} catch (Exception $e) {
    echo "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
