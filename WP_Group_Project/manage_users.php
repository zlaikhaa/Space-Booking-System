<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

//$search = $_GET['search'] ?? '';
/*$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = $search ? "WHERE id_number LIKE '%$search%' OR username LIKE '%$search%'" : "";
$result = mysqli_query($conn, "SELECT id_number, username, role FROM users $searchQuery ORDER BY role, username");
*/
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = $search ? "WHERE id_number LIKE '%$search%' OR username LIKE '%$search%'" : "";

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'username_asc';

// Define allowed sort columns and directions to prevent SQL injection
$allowedSorts = [
    'username_asc' => ['column' => 'username', 'direction' => 'ASC'],
    'username_desc' => ['column' => 'username', 'direction' => 'DESC'],
    'id_number_asc' => ['column' => 'id_number', 'direction' => 'ASC'],
    'id_number_desc' => ['column' => 'id_number', 'direction' => 'DESC'],
    'role_asc' => ['column' => 'role', 'direction' => 'ASC'],
    'role_desc' => ['column' => 'role', 'direction' => 'DESC'],
];

if (array_key_exists($sort, $allowedSorts)) {
    $orderBy = $allowedSorts[$sort]['column'] . ' ' . $allowedSorts[$sort]['direction'];
} else {
    // Default sorting if invalid sort param
    $orderBy = 'username ASC';
}

$query = "SELECT id_number, username, role FROM users $searchQuery ORDER BY $orderBy";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Space Booking System - Manage Users</h1>
    <nav>
        <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>User List</h2>

    <?php if (isset($_GET['success'])): ?>
        <p class="message">
            <?= htmlspecialchars($_GET['success']) === 'added' ? "User added successfully!" :
                ($_GET['success'] === 'edited' ? "User updated!" :
                ($_GET['success'] === 'deleted' ? "User deleted!" : "")) ?>
        </p>
    <?php elseif (isset($_GET['error']) && $_GET['error'] === 'cannot_delete_self'): ?>
        <p class="error">You cannot delete your own account.</p>
    <?php endif; ?>
    <!--
    <div class="search-bar">
        <form method="get">
            <input type="text" name="search" placeholder="Search by ID or username" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn">Search</button>
            <a href="add_user.php" class="btn">Add New User</a>
        </form>
    </div>
    -->
    <div class="search-bar">
        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Search by username" value="<?= htmlspecialchars($search) ?>">

            <select name="sort">
                <option value="username_asc" <?= $sort === 'username_asc' ? 'selected' : '' ?>>Username (A–Z)</option>
                <option value="username_desc" <?= $sort === 'username_desc' ? 'selected' : '' ?>>Username (Z–A)</option>
                <option value="id_number_asc" <?= $sort === 'id_number_asc' ? 'selected' : '' ?>>ID Number (A–Z)</option>
                <option value="id_number_desc" <?= $sort === 'id_number_desc' ? 'selected' : '' ?>>ID Number (Z–A)</option>
                <option value="role_asc" <?= $sort === 'role_asc' ? 'selected' : '' ?>>Role (A–Z)</option>
                <option value="role_desc" <?= $sort === 'role_desc' ? 'selected' : '' ?>>Role (Z–A)</option>
            </select>

            <button type="submit" class="btn">Search</button>
            <a href="add_user.php" class="btn">Add New User</a>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id_number']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= ucfirst($user['role']) ?></td>
                    <td class="actions">
                        <a href="edit_user.php?id_number=<?= urlencode($user['id_number']) ?>">Edit</a>
                        <?php if ($_SESSION['id_number'] !== $user['id_number']): ?>
                            <a href="delete_user.php?id_number=<?= urlencode($user['id_number']) ?>" class="delete"
                               onclick="return confirm('Delete this user?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>
<?php include "footer.php"; ?>
</body>
</html>