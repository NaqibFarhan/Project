<?php
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    mysqli_query($conn, "INSERT INTO students (name, class) VALUES ('$name', '$class')");
    echo "<script>alert('Student added successfully'); window.location.href='students.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Student - SmartTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <style>
    .container {
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: #800000;
      color: white;
      height: 100vh;
      padding: 20px;
      box-sizing: border-box;
    }

    .sidebar a {
      color: white;
      display: block;
      margin: 10px 0;
      text-decoration: none;
    }

    .logo-container {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar-logo {
      width: 100px;
      height: auto;
      margin-bottom: 10px;
    }

    .main-content {
      flex-grow: 1;
      padding: 40px;
      background: #f4f4f4;
      min-height: 100vh;
      text-align: center;
    }

    .form-box {
      max-width: 400px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: left;
    }

    input[type="text"], .button {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }

    .button {
      background-color: #2ecc71;
      color: white;
      border: none;
      cursor: pointer;
    }

    .button:hover {
      background-color: #27ae60;
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo-container">
      <img src="images/logo.png" alt="System Logo" class="sidebar-logo">
      <h2>SmartTrack</h2>
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="attendance.php">Take Attendance</a>
    <a href="view_attendance.php">Attendance Records</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <!-- Logo -->
    <div>
      <img src="images/logo.png" alt="System Logo" style="width: 100px; height: auto; margin-bottom: 10px;">
    </div>

    <!-- Heading -->
    <h2 style="margin-bottom: 20px;">Add New Student</h2>

    <!-- Form -->
    <div class="form-box">
      <form method="POST" onsubmit="return confirm('Are you sure you want to add this student?');">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Class:</label><br>
        <input type="text" name="class" required><br>

        <input type="submit" value="Add Student" class="button">
      </form>
    </div>
  </div>
</div>
</body>
</html>
