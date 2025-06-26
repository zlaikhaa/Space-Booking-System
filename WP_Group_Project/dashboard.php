<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['role']) {
    case 'admin': header("Location: admin_dashboard.php"); break;
    case 'lecturer': header("Location: lecturer_dashboard.php"); break;
    case 'manager': header("Location: manager_dashboard.php"); break;
}
exit();