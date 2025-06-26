<?php
session_start();
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<header>
    <h1>Welcome, Lecturer <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <nav>
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Space Booking System</h2>
    <div class="lecturer">
        <a href="book_room.php" class="btn">Book a Room 📅</a>
        <a href="view_booking.php" class="btn">View My Bookings 🗂️</a>
    </div>
</main>
<?php include "footer.php"; ?>
</body>
</html>