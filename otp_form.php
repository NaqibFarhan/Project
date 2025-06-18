<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
<style>
/* Global Styles */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(-45deg, #4e54c8, #8f94fb, #6a82fb, #a18cd1);
    background-size: 400% 400%;
    animation: gradientBG 12s ease infinite;
}

/* Animate background gradient */
@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* OTP Box Container */
.login-box {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: 350px;
}

/* Heading */
.login-box h2 {
    margin-bottom: 20px;
    color: #333;
}

/* Form Input Fields */
.login-box input[type="text"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

/* Submit Button */
.login-box button {
    background-color: #4e54c8;
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.login-box button:hover {
    background-color: #3a3fc1;
}
</style>

</head>
<body>
<div class="login-box">
    <h2>Enter OTP</h2>
    <form action="verify_otp.php" method="POST">
        <input type="text" name="otp" placeholder="6-digit OTP" required>
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>
