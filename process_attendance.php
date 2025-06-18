<?php
include("includes/db.php");

$secret_key = 'your_secure_key_123'; // Must match key in qr_attendance.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = intval($_POST['student_id']);
    $date = $_POST['date'];
    
    
    // Verify token
    $expected_token = hash_hmac('sha256', 'attendance_' . $date, $secret_key);
    
    if (!hash_equals($expected_token, $token)) {
        die("Invalid token");
    }
    
    // Check if attendance exists
    $check = mysqli_prepare($conn, "SELECT id FROM attendance WHERE student_id = ? AND date = ?");
    mysqli_stmt_bind_param($check, "is", $student_id, $date);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    
    if (mysqli_stmt_num_rows($check) == 0) {
        // Insert attendance
        $insert = mysqli_prepare($conn, "INSERT INTO attendance (student_id, date, status) VALUES (?, ?, 'Present')");
        mysqli_stmt_bind_param($insert, "is", $student_id, $date);
        mysqli_stmt_execute($insert);
        
        echo "<script>alert('Attendance recorded!'); window.location.href='qr_attendance.php';</script>";
    } else {
        echo "<script>alert('Attendance already recorded'); window.location.href='qr_attendance.php';</script>";
    }
}