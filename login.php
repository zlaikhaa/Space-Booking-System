<?php
session_start();
require 'db.php';

//$remembered_user = $_COOKIE['remembered_user'] ?? '';
//$remembered_pass = $_COOKIE['remembered_pass'] ?? '';
$remembered_user = isset($_COOKIE['remembered_user']) ? $_COOKIE['remembered_user'] : '';
$remembered_pass = isset($_COOKIE['remembered_pass']) ? $_COOKIE['remembered_pass'] : '';
$error = '';

// AUTO LOGIN if cookies exist and session not already set
if (!isset($_SESSION['id_number']) && $remembered_user && $remembered_pass) {
    $query = "SELECT * FROM users WHERE id_number = ? OR username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $remembered_user, $remembered_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && $user['password'] === $remembered_pass) {
        $_SESSION['id_number'] = $user['id_number'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'manager') {
            header("Location: manager_dashboard.php");
        } else {
            header("Location: lecturer_dashboard.php");
        }
        exit();
    }
}

// Normal login via form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = trim($_POST['id_or_username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    $query = "SELECT * FROM users WHERE id_number = ? OR username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $input, $input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && $user['password'] === $password) {
        $_SESSION['id_number'] = $user['id_number'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Set cookies if "Remember Me" is checked
        if ($remember) {
            setcookie('remembered_user', $input, time() + (86400 * 30), "/");
            setcookie('remembered_pass', $password, time() + (86400 * 30), "/");
        } else {
            setcookie('remembered_user', '', time() - 3600, "/");
            setcookie('remembered_pass', '', time() - 3600, "/");
        }

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'manager') {
            header("Location: manager_dashboard.php");
        } else {
            header("Location: lecturer_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="login-header">
    <h1>Welcome to Space Booking System</h1>
</header>
<div class="form-container">
    <h2>Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <!--<form method="post">
        <input type="text" name="id_or_username" placeholder="ID Number or Username" required value="<?= htmlspecialchars($remembered_user) ?>">
        <input type="password" name="password" placeholder="Password" required value="<?= htmlspecialchars($remembered_pass) ?>">
        <button type="submit">Login</button>
        <label>
            <input type="checkbox" name="remember" <?= $remembered_user ? 'checked' : '' ?>> Remember Me
        </label>
        <p><a href="register.php">Register</a></p>
    </form>
    -->
    <form method="post">
    <input type="text" name="id_or_username" placeholder="ID Number or Username" required value="<?= htmlspecialchars($remembered_user) ?>">

    <input type="password" name="password" placeholder="Password" required value="<?= htmlspecialchars($remembered_pass) ?>">

    <div class="checkbox-container">
        <input type="checkbox" id="remember" name="remember" <?= $remembered_user ? 'checked' : '' ?>>
        <label for="remember">Remember Me</label>
    </div>

    <button type="submit">Login</button>

    <p class="register-link"><a href="register.php">Don't have an account? Register</a></p>
</form>
</form>
</div>
</body>
<?php include "footer.php"; ?>
</html>