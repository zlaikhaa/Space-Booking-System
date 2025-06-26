<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_number']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_spaces.php");
    exit();
}

$id = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM spaces WHERE id = $id");
$space = mysqli_fetch_assoc($result);

if (!$space) {
    die("Space not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $room_name = mysqli_real_escape_string($conn, $_POST['room_name']);

    $update = "UPDATE spaces SET building='$building', room_name='$room_name' WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        header("Location: manage_spaces.php");
        exit();
    } else {
        $error = "Update failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Space</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Edit Space</h1>
    <nav>
        <a href="<?= $_SESSION['role'] ?>_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="form-container">
    <h2>Edit Space</h2>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label>Building:</label>
        <input type="text" name="building" value="<?= htmlspecialchars($space['building']) ?>" required>

        <label>Room Name:</label>
        <input type="text" name="room_name" value="<?= htmlspecialchars($space['room_name']) ?>" required>

        <button type="submit">Update</button>
        <p><a href="manage_spaces.php">⬅ Back</a></p>
    </form>
</div>
</body>
<?php include "footer.php"; ?>
</html>
