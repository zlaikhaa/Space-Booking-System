<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "db.php";
$user_id = $_SESSION['user_id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $updateQuery = "UPDATE users SET name='$name', email='$email' WHERE id=$user_id";
    if (mysqli_query($conn, $updateQuery)) {
        $msg = "Profile updated successfully.";
    } else {
        $msg = "Update failed.";
    }
}

$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <h1>Edit Profile</h1>
  <nav><a href="dashboard.php">Dashboard</a> <a href="logout.php">Logout</a></nav>
</header>

<main>
  <?php if ($msg) echo "<p>$msg</p>"; ?>
  <form method="post">
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></label><br>
    <input type="submit" value="Update">
  </form>
</main>
</body>
<?php include "footer.php"; ?>
</html>
