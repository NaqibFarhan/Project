<?php
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

$today = date('Y-m-d');
$secret_key = 'your_secure_key_123'; // Change this to your own secret
$token = hash_hmac('sha256', 'attendance_' . $today, $secret_key);

// Get students list
$students = mysqli_query($conn, "SELECT id, name, class FROM students ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>QR Attendance - SmartTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <!-- Using CDN for QR code library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        #qrCode {
            width: 250px;
            height: 250px;
            margin: 20px auto;
            border: 1px solid #ddd;
            padding: 10px;
            background: white;
            display: block !important;
    visibility: visible !important;
        }
        .qr-container {
            text-align: center;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2>QR Code Attendance</h2>
        
        <div class="qr-container">
            <h3>Today's Attendance Code</h3>
            <p><?php echo $today; ?></p>
            
            <!-- QR Code will appear here -->
            <div id="qrCode"></div>
            
            <p>Scan this code with the SmartTrack mobile app</p>
        </div>

        <div class="card">
            <h3>Manual Attendance</h3>
            <form method="post" action="process_attendance.php">
                <label>Select Student:</label>
                <select name="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php while($row = mysqli_fetch_assoc($students)): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['class']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="hidden" name="date" value="<?= $today ?>">
                <input type="hidden" name="token" value="<?= $token ?>">
                <button type="submit" class="btn-action">Mark Present</button>
            </form>
        </div>
    </div>

    <script>
        // Simple QR code generation
        document.addEventListener('DOMContentLoaded', function() {
            const qrData = {
                system: "SmartTrack",
                date: "<?= $today ?>",
                token: "<?= $token ?>",
                action: "attendance"
            };
            
            // Generate QR code
            new QRCode(document.getElementById("qrCode"), {
                text: JSON.stringify(qrData),
                width: 250,
                height: 250,
                colorDark: "#800000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            console.log("QR code should be visible now");
        });
    </script>
</body>
</html>