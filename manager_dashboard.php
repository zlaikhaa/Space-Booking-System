<?php
session_start();
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'manager') {
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
    <h1>Welcome, Manager <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
    <nav>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
        <?php elseif ($_SESSION['role'] === 'manager'): ?>
            <a href="manager_dashboard.php">⬅ Back to Dashboard</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Manager Controls</h2>
    <div class="admin-controls">
        <a href="manage_bookings.php" class="btn">Manage Bookings 📅</a>
        <a href="view_report.php" class="btn">View Reports 📄</a>
    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
