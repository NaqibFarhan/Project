<?php
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

// Initialize feedback message
$feedback = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $success_count = 0;
    $error_count = 0;

    foreach ($_POST['attendance'] as $student_id => $status) {
        $student_id = intval($student_id);
        $status = mysqli_real_escape_string($conn, $status);

        // Validate status
        if (!in_array($status, ['Attend', 'Absent'])) {
            $status = 'Attend';
        }

        $query = "REPLACE INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')";

        if (mysqli_query($conn, $query)) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    // Feedback message
    if ($error_count == 0) {
        $feedback = "<div class='alert-success'>✅ Attendance saved successfully for $date ($success_count records).</div>";
    } else {
        $feedback = "<div class='alert-warning'>⚠️ Attendance saved with $error_count error(s). $success_count record(s) saved.</div>";
    }
}

// Fetch student list
$students = mysqli_query($conn, "SELECT * FROM students ORDER BY name ASC");
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Attendance - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
        }
        .main-content {
            padding: 30px;
        }
        .attendance-form {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .form-header label {
            font-weight: bold;
        }
        .form-header input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }
        .quick-actions {
            margin: 15px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-quick {
            background: #eee;
            padding: 8px 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-quick:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .student-count {
            text-align: right;
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        thead {
            background: #007bff;
            color: white;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .btn-submit {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
            width: 100%;
        }
        .btn-submit:hover {
            background: #218838;
        }
        .alert-success, .alert-warning {
            padding: 12px;
            margin-bottom: 20px;
            border-left: 5px solid;
            border-radius: 6px;
        }
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        @media (max-width: 768px) {
            .form-header {
                flex-direction: column;
                align-items: flex-start;
            }
            table {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="attendance-form">
        <h2>📝 Take Attendance</h2>
        <?= $feedback ?>
        <form method="POST" id="attendanceForm">
            <div class="form-header">
                <label for="date">📅 Select Date:</label>
                <input type="date" name="date" id="date" required value="<?= $today ?>" max="<?= $today ?>">
            </div>

            <div class="quick-actions">
                <button type="button" class="btn-quick" onclick="markAllAttend()">✅ Mark All Present</button>
                <button type="button" class="btn-quick" onclick="markAllAbsent()">❌ Mark All Absent</button>
                <button type="button" class="btn-quick" onclick="resetForm()">🔄 Reset</button>
            </div>

            <?php $student_count = mysqli_num_rows($students); ?>
            <div class="student-count">Total Students: <strong><?= $student_count ?></strong></div>

            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($student_count > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($students)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['class']) ?></td>
                                <td>
                                    <select name="attendance[<?= $row['id'] ?>]" class="attendance-select" required>
                                        <option value="Attend" selected>✅ Attend</option>
                                        <option value="Absent">❌ Absent</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">No students found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($student_count > 0): ?>
                <button type="submit" class="btn-submit">💾 Save Attendance for <?= date('F j, Y', strtotime($today)) ?></button>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
    $('#date').on('change', function () {
        const selectedDate = new Date($(this).val());
        const formatted = selectedDate.toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'});
        $('.btn-submit').text('💾 Save Attendance for ' + formatted);
    });

    function markAllAttend() {
        $('.attendance-select').val('Attend');
    }

    function markAllAbsent() {
        $('.attendance-select').val('Absent');
    }

    function resetForm() {
        $('.attendance-select').val('Attend');
    }

    $('#attendanceForm').on('submit', function (e) {
        const date = $('#date').val();
        if (!date) {
            alert('Please select a date.');
            e.preventDefault();
            return;
        }

        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate > today) {
            alert('Cannot select a future date.');
            e.preventDefault();
            return;
        }

        const attend = $('.attendance-select option[value="Attend"]:selected').length;
        const absent = $('.attendance-select option[value="Absent"]:selected').length;

        const confirmMsg = `Save attendance for ${date}?\nPresent: ${attend} students\nAbsent: ${absent} students`;
        if (!confirm(confirmMsg)) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>
