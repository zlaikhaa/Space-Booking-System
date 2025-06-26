<?php
session_start();
require 'db.php';

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
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Register</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="login-header">
    <h1>Welcome to Space Booking System</h1>
</header>
<div class="form-container">
    <h2>Register</h2>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <input type="text" name="id_number" placeholder="ID Number" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="" disabled selected>Select role</option>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <option value="admin">Admin</option>
            <?php endif; ?>
            <option value="lecturer">Lecturer</option>
            <option value="manager">Space Manager</option>
        </select>
        <button type="submit">Register</button>
        <p><a href="login.php">Back to login</a></p>
    </form>
</div>
</body>
<?php include "footer.php"; ?>
</html>