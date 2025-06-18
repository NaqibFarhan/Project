<?php
session_start();
include("includes/sidebar.php");
session_destroy();
header("Location: login.php");
?>
