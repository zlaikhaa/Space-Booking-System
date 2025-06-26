<!-- index.php -->
<?php session_start(); 
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Space Booking Management</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="main-header">
    <h1>Welcome to Space Booking System</h1>
    <nav class="nav-links">
      <a href="index.php" class="nav-btn">Home</a>
      <a href="login.php" class="nav-btn">Login</a>
      <a href="register.php" class="nav-btn">Register</a>
    </nav>
  </header>

  <main>
    <section class="intro">
      <h2>Manage Room & Lab Bookings Easily</h2>
      <p>This system allows <strong>lecturers</strong> to book rooms, <strong>space managers</strong> to approve bookings, and <strong>admins</strong> to manage users.</p>
      <a class="btn primary-btn" href="login.php">Get Started</a>
    </section>
  </main>

  <?php include "footer.php"; ?>

    <!--
    <footer>
      <p>&copy; <?php echo date("Y"); ?> UTM Space Booking System</p>
    </footer>
    -->
</body>
</html>