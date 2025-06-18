<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$today = date('Y-m-d');

// Total students
$total_students_query = "SELECT COUNT(*) as total FROM students";
$total_students_result = mysqli_query($conn, $total_students_query);
$total_students = ($total_students_result) ? mysqli_fetch_assoc($total_students_result)['total'] : 0;

// Today's attendance counts
$attend_query = "SELECT COUNT(DISTINCT student_id) as count FROM attendance WHERE date = '$today' AND LOWER(status) = 'attend'";
$absent_query = "SELECT COUNT(DISTINCT student_id) as count FROM attendance WHERE date = '$today' AND LOWER(status) = 'absent'";
$attend_count = ($res = mysqli_query($conn, $attend_query)) ? mysqli_fetch_assoc($res)['count'] : 0;
$absent_count = ($res = mysqli_query($conn, $absent_query)) ? mysqli_fetch_assoc($res)['count'] : 0;
$total_marked = $attend_count + $absent_count;
$attendance_percentage = $total_marked > 0 ? round(($attend_count / $total_marked) * 100, 1) : 0;
$not_marked = $total_students - $total_marked;

// Total attendance records (all-time)
$total_attendance_query = "SELECT COUNT(*) as total FROM attendance";
$total_attendance_result = mysqli_query($conn, $total_attendance_query);
$total_attendance_all = ($total_attendance_result) ? mysqli_fetch_assoc($total_attendance_result)['total'] : 0;

// Attendance over the past 7 days
$past_week_query = "SELECT date, COUNT(*) as total FROM attendance WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY date ORDER BY date DESC";
$past_week_result = mysqli_query($conn, $past_week_query);
$past_week_data = [];
while ($row = mysqli_fetch_assoc($past_week_result)) {
    $past_week_data[] = $row;
}

// Most recent attendance record
$latest_attendance_query = "SELECT * FROM attendance ORDER BY date DESC, id DESC LIMIT 1";
$latest_result = mysqli_query($conn, $latest_attendance_query);
$latest_attendance = ($latest_result && mysqli_num_rows($latest_result) > 0) ? mysqli_fetch_assoc($latest_result) : null;

// Students not marked today
$unmarked_query = "
    SELECT * FROM students 
    WHERE id NOT IN (
        SELECT DISTINCT student_id 
        FROM attendance 
        WHERE date = '$today'
    )
";

$unmarked_result = mysqli_query($conn, $unmarked_query);
$unmarked_students = [];
while ($row = mysqli_fetch_assoc($unmarked_result)) {
    $unmarked_students[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 40px;
        }
        .main-content h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .main-content p {
            margin-bottom: 30px;
            color: #555;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-left: 5px solid #007bff;
        }
        .card h3 {
            font-size: 1em;
            color: #555;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card h1 {
            font-size: 2.8em;
            margin: 0;
            color: #2c3e50;
        }
        .card p {
            margin-top: 10px;
            font-size: 1em;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background: #f8f8f8;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="main-content">
        <h1>📊 Dashboard</h1>
        <p>Welcome to the Student Attendance Management System</p>

        <!-- Top cards -->
        <div class="card-container">
            <div class="card">
                <h3>👥 Total Students</h3>
                <h1><?= number_format($total_students) ?></h1>
                <p>Students registered</p>
            </div>
            <div class="card">
                <h3>📋 Marked Today</h3>
                <h1><?= number_format($total_marked) ?></h1>
                <p>Present: <?= $attend_count ?> | Absent: <?= $absent_count ?></p>
                <p>Not Marked: <?= $not_marked ?></p>
            </div>
            <div class="card">
                <h3>📈 Attendance Rate</h3>
                <h1><?= $attendance_percentage ?>%</h1>
                <p>For today's attendance</p>
            </div>
        </div>

        <!-- More stats -->
        <div class="card-container">
            <div class="card" style="border-left-color: #28a745;">
                <h3>🗂 Total Attendance Records</h3>
                <h1><?= number_format($total_attendance_all) ?></h1>
                <p>All-time entries</p>
            </div>
            <?php if ($latest_attendance): ?>
            <div class="card" style="border-left-color: #17a2b8;">
                <h3>🕓 Latest Entry</h3>
                <h1><?= htmlspecialchars($latest_attendance['student_id']) ?></h1>
                <p><?= htmlspecialchars($latest_attendance['status']) ?> on <?= $latest_attendance['date'] ?></p>
            </div>
            <?php endif; ?>
            <div class="card" style="border-left-color: #ffc107;">
                <h3>🚫 Not Marked Today</h3>
                <h1><?= count($unmarked_students) ?></h1>
                <p>Unmarked students</p>
            </div>
        </div>

        <!-- Weekly summary -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 30px;">
            <h3>🗕 Attendance in Past 7 Days</h3>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Total Records</th>
                </tr>
                <?php foreach ($past_week_data as $day): ?>
                    <tr>
                        <td><?= $day['date'] ?></td>
                        <td><?= $day['total'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- Chart canvas -->
            <canvas id="attendanceChart" style="max-height: 300px; margin-top: 30px;"></canvas>
            <script>
                const labels = <?= json_encode(array_column(array_reverse($past_week_data), 'date')) ?>;
                const data = <?= json_encode(array_column(array_reverse($past_week_data), 'total')) ?>;

                const ctx = document.getElementById('attendanceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Attendance Records',
                            data: data,
                            fill: true,
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderColor: '#007bff',
                            borderWidth: 2,
                            tension: 0.3,
                            pointBackgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Records' }
                            },
                            x: {
                                title: { display: true, text: 'Date' }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>
</body>
</html>
