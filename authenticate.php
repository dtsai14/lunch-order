<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /lunchorder/login.php");
    exit;
}
?>