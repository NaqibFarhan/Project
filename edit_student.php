<?php
include("includes/auth.php");
include("includes/db.php");

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    mysqli_query($conn, "UPDATE students SET name='$name', class='$class' WHERE id=$id");
    echo "<script>alert('Student updated successfully'); window.location.href='students.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Student - SmartTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <div class="sidebar">
    <h2>SmartTrack</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="attendance.php">Take Attendance</a>
    <a href="view_attendance.php">Attendance Records</a>
    <a href="logout.php">Logout</a>
  </div>
  <div class="main-content">
    <h2>Edit Student</h2>
    <form method="POST">
      <label>Name:</label><br>
      <input type="text" name="name" value="<?php echo $student['name']; ?>" required><br><br>
      
      <label>Class:</label><br>
      <input type="text" name="class" value="<?php echo $student['class']; ?>" required><br><br>

      <input type="submit" value="Update Student" class="button">
    </form>
  </div>
</div>
</body>
</html>
