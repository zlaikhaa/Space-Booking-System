<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $role     = trim($_POST['role']);

    // Validate allowed roles
    $allowedRoles = ['lecturer', 'manager'];
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $allowedRoles[] = 'admin'; // Admin can create admin
    }

    if (!in_array($role, $allowedRoles)) {
        die("Invalid role selected.");
    }

    // Check if username or email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($check, "ss", $username);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        echo "<p class='error'>Username or Email already exists!</p>";
        exit();
    }

    // Insert new user
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $role);

    if (mysqli_stmt_execute($stmt)) {
        echo "<p class='message'>User registered successfully!</p>";
        echo "<a class='back-link' href='login.php'>Go to Login</a>";
    } else {
        echo "<p class='error'>Error: " . mysqli_error($conn) . "</p>";
    }

    mysqli_stmt_close($stmt);
}
?>
