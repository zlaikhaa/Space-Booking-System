<?php
session_start();
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Welcome, Admin <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
    <nav>
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Admin Controls</h2>
    <div class="admin-controls">
        <a href="manage_users.php" class="btn">Manage Users 👤</a>
        <a href="manage_bookings.php" class="btn">Manage Bookings 📅</a>
        <a href="manage_spaces.php" class="btn">Manage Spaces 🏫</a>
        <a href="view_report.php" class="btn">View Reports 📄</a>
    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
