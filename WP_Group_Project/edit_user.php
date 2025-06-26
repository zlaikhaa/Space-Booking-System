<?php
session_start();
require 'db.php';

// Only admin can access
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Ensure id_number is passed
if (!isset($_GET['id_number'])) {
    header("Location: manage_users.php");
    exit();
}

$id_number = mysqli_real_escape_string($conn, $_GET['id_number']);
$error = '';
$success = '';

// Fetch user by id_number
$result = mysqli_query($conn, "SELECT * FROM users WHERE id_number = '$id_number'");
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $error = "User not found.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $new_id_number = trim($_POST['id_number']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = trim($_POST['role']);

    // Validation rules (server-side backup)
    if (!preg_match('/^[A-Z]\d{3,9}$/', $new_id_number)) {
        $error = "ID number must start with a capital letter followed by 3 to 9 digits (e.g., M2001).";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 characters (letters, numbers, underscore).";
    } elseif (!in_array($role, ['lecturer', 'manager', 'admin'])) {
        $error = "Invalid role selected.";
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Escape for SQL
        $new_id_number_sql = mysqli_real_escape_string($conn, $new_id_number);
        $username_sql = mysqli_real_escape_string($conn, $username);
        $role_sql = mysqli_real_escape_string($conn, $role);

        // Build update query
        $update = "UPDATE users SET id_number='$new_id_number_sql', username='$username_sql', role='$role_sql'";

        if (!empty($password)) {
            // Hash the password before storing
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $password_hash_sql = mysqli_real_escape_string($conn, $password_hash);
            $update .= ", password='$password_hash_sql'";
        }
        // Use original $id_number to identify the row to update
        $id_number_sql = mysqli_real_escape_string($conn, $id_number);
        $update .= " WHERE id_number='$id_number_sql'";

        if (mysqli_query($conn, $update)) {
            // Redirect after successful update
            header("Location: manage_users.php");
            exit();
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            // Get form values
            const idNumber = form.id_number.value.trim();
            const username = form.username.value.trim();
            const password = form.password.value;
            const role = form.role.value;

            // Validate id_number: must start with uppercase letter + 3-9 digits
            if (!/^[A-Z]\d{3,9}$/.test(idNumber)) {
                alert("ID number must start with a capital letter followed by 3 to 9 digits (e.g., M2001).");
                event.preventDefault();
                return;
            }

            // Validate username: 3-20 chars, letters/numbers/underscore
            if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
                alert("Username must be 3-20 characters (letters, numbers, underscore).");
                event.preventDefault();
                return;
            }

            // Validate role is one of allowed
            if (!['lecturer', 'manager', 'admin'].includes(role)) {
                alert("Invalid role selected.");
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
    <h1>Edit User</h1>
    <nav>
        <a href="<?= htmlspecialchars($_SESSION['role']) ?>_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
<div class="form-container">
    <h2>Edit User</h2>

    <?php if ($error): ?>
    <script>
        alert("<?= addslashes($error) ?>");
    </script>
    <?php endif; ?>

    <?php if ($user): ?>
        <form method="post" novalidate>
            <label for="id_number">ID Number</label>
            <input
                type="text"
                id="id_number"
                name="id_number"
                value="<?= htmlspecialchars(isset($_POST['id_number']) ? $_POST['id_number'] : $user['id_number']) ?>"
                pattern="[A-Z]\d{3,9}"
                title="Starts with a capital letter followed by 3 to 9 digits (e.g., M2001)"
                required
            >

            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : $user['username']) ?>"
                pattern="[a-zA-Z0-9_]{3,20}"
                title="3-20 characters: letters, numbers, underscore"
                required
            >

            <label for="password">New Password (Leave blank to keep current)</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="New password"
                minlength="6"
            >

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="lecturer" <?= ((isset($_POST['role']) && $_POST['role'] == 'lecturer') || (!isset($_POST['role']) && $user['role'] == 'lecturer')) ? 'selected' : '' ?>>Lecturer</option>
                <option value="manager" <?= ((isset($_POST['role']) && $_POST['role'] == 'manager') || (!isset($_POST['role']) && $user['role'] == 'manager')) ? 'selected' : '' ?>>Space Manager</option>
                <option value="admin" <?= ((isset($_POST['role']) && $_POST['role'] == 'admin') || (!isset($_POST['role']) && $user['role'] == 'admin')) ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit">Update</button>
            <p><a href="manage_users.php">⬅ Back</a></p>
        </form>
    <?php endif; ?>
</div>

</body>
<?php include "footer.php"; ?>
</html>
