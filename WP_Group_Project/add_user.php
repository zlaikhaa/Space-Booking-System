<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_POST['id_number'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR id_number='$id_number'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username or ID number already exists.";
    } else {
        mysqli_query($conn, "INSERT INTO users (id_number, username, password, role) VALUES ('$id_number', '$username', '$password', '$role')");
        header("Location: manage_users.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Add User</title><link rel="stylesheet" href="style.css"></head>
<body>

<header>
    <h1>Space Booking System - Add User</h1>
    <nav>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
        <?php else: ?>
            <a href="manager_dashboard.php">⬅ Back to Dashboard</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="form-container">
    <h2>Add New User</h2>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <input type="text" name="id_number" placeholder="ID Number" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="lecturer">Lecturer</option>
            <option value="manager">Space Manager</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Add User</button>
        <p><a href="manage_users.php">⬅ Back to User List</a></p>
    </form>
</div>
</body>
<?php include "footer.php"; ?>
</html>