<?php
header("Content-Type: application/json");

// Replace with your own DB credentials
$host = "localhost";
$dbname = "smarttrack";
$username = "root";
$password = "";

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['student_id'], $data['date'], $data['token'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$studentId = $data['student_id'];
$date = $data['date'];
$token = $data['token'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check for duplicate entry
    $check = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE student_id = ? AND date = ?");
    $check->execute([$studentId, $date]);

    if ($check->fetchColumn() > 0) {
        echo json_encode(["success" => false, "message" => "Attendance already recorded"]);
        exit;
    }

    // Insert attendance
    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, token) VALUES (?, ?, ?)");
    $stmt->execute([$studentId, $date, $token]);

    echo json_encode(["success" => true, "message" => "Attendance submitted successfully"]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
