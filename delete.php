<?php
include("includes/auth.php");
include("includes/db.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
    header("Location: students.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
