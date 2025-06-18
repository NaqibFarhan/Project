<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");
?>
<!DOCTYPE html>
<head>
  <title>Students - SmartTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .main-content h2 {
      margin-bottom: 20px;
      font-size: 1.8em;
      color: #2c3e50;
    }
    .btn-action {
      padding: 8px 16px;
      background-color: #007bff;
      color: white;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }
    .btn-action:hover {
      background-color: #0056b3;
    }
    table {
      background: white;
      border-collapse: collapse;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      border-radius: 8px;
      overflow: hidden;
    }
    th {
      background: #007bff;
      color: white;
      padding: 12px;
      text-align: left;
    }
    td {
      padding: 12px;
      border-bottom: 1px solid #eee;
    }
    tr:hover td {
      background: #f9f9f9;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="main-content">
    <h2>📚 Student List</h2>
    <a href="add_student.php" class="btn-action" style="text-decoration: none; margin-bottom: 20px; display: inline-block;">➕ Add Student</a>
    <table style="width: 100%;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Class</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT * FROM students";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td>".$row['id']."</td>";
          echo "<td>".$row['name']."</td>";
          echo "<td>".$row['class']."</td>";
          echo "<td>
                  <a href='edit_student.php?id=".$row['id']."' class='btn-action' style='text-decoration: none;'>✏️ Edit</a>
                  <a href='delete.php?id=".$row['id']."' class='btn-action' style='text-decoration: none; background-color: #dc3545; margin-left: 10px;' onclick='return confirm(\"Are you sure?\")'>🗑️ Delete</a>
                </td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
