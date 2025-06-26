<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_number'])) {
    header("Location: login.php");
    exit();
}

$id_number = $_SESSION['id_number'];
$success = $error = '';

// Fetch current user info
$result = mysqli_query($conn, "SELECT * FROM users WHERE id_number = '".mysqli_real_escape_string($conn, $id_number)."'");
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Server-side validation
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 characters: letters, numbers, underscore.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $username_sql = mysqli_real_escape_string($conn, $username);

        $update = "UPDATE users SET username='$username_sql'";

        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $password_hash_sql = mysqli_real_escape_string($conn, $password_hash);
            $update .= ", password='$password_hash_sql'";
        }

        $id_number_sql = mysqli_real_escape_string($conn, $id_number);
        $update .= " WHERE id_number='$id_number_sql'";

        if (mysqli_query($conn, $update)) {
            $success = "Profile updated successfully.";
            $_SESSION['username'] = $username; // Update session

            // Refresh user data from database
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id_number = '$id_number_sql'");
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="style.css">

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            const username = form.username.value.trim();
            const password = form.password.value;

            // Validate username: 3-20 chars letters/numbers/underscore
            if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
                alert("Username must be 3-20 characters: letters, numbers, underscore.");
                event.preventDefault();
                return;
            }

            // Validate password length if entered
            if (password.length > 0 && password.length < 6) {
                alert("Password must be at least 6 characters.");
                event.preventDefault();
                return;
            }
        });
    });
    </script>
</head>
<body>

<header>
    <h1>My Profile</h1>
    <nav>
        <a href="<?= htmlspecialchars($_SESSION['role']) ?>_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="form-container">
    <h2>Edit Profile</h2>
    <?php if ($success): ?><p class="message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($error): ?><script>alert("<?= addslashes($error) ?>");</script><?php endif; ?>

    <form method="post" novalidate>
        <label>ID Number:</label>
        <input type="text" value="<?= htmlspecialchars($user['id_number']) ?>" disabled>

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : $user['username']) ?>" required>

        <label>New Password:</label>
        <input type="password" name="password" placeholder="New Password">

        <button type="submit">Save Changes</button>
    </form>
</main>

<?php include "footer.php"; ?>
</body>
</html>
