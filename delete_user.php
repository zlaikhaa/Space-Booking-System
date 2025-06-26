<?php
session_start();
require 'db.php';

// Only admin can delete users
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if id_number is provided
if (isset($_GET['id_number'])) {
    $id_number = $_GET['id_number'];

    // Prevent deleting self
    if ($_SESSION['id_number'] == $id_number) {
        echo "<script>alert('You cannot delete your own account.'); window.location.href='manage_users.php';</script>";
        exit();
    }

    // Perform deletion
    $delete = "DELETE FROM users WHERE id_number = '$id_number'";
    if (mysqli_query($conn, $delete)) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
} else {
    header("Location: manage_users.php");
    exit();
}
?>
