<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: /lunchorder/lunchorder.php");
    exit;
}
?>