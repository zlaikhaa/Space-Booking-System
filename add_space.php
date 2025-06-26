<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_number']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $building = trim($_POST['building']);
    $room_name = trim($_POST['room_name']);

    if ($building !== '' && $room_name !== '') {
        $buildingEsc = mysqli_real_escape_string($conn, $building);
        $roomEsc = mysqli_real_escape_string($conn, $room_name);

        $result = mysqli_query($conn, "INSERT INTO spaces (building, room_name) VALUES ('$buildingEsc', '$roomEsc')");
        if ($result) {
            header("Location: manage_spaces.php?added=1");
            exit();
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Space</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Space Booking System - Add Space</h1>
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
    <h2>Add New Space</h2>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" novalidate>
        <input type="text" name="building" placeholder="Building (e.g. L50)" required value="<?= isset($_POST['building']) ? htmlspecialchars($_POST['building']) : '' ?>">
        <input type="text" name="room_name" placeholder="Room Name (e.g. Dewan Kuliah 1)" required value="<?= isset($_POST['room_name']) ? htmlspecialchars($_POST['room_name']) : '' ?>">
        <button type="submit" class="btn">Add Space</button>
        <p><a href="manage_spaces.php">⬅ Back</a></p>
    </form>
</div>

</body>
<?php include "footer.php"; ?>
</html>