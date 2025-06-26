<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete space
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM spaces WHERE id = $id");
    header("Location: manage_spaces.php?deleted=1");
    exit();
}

// Get search, sort, and building filter values
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_asc';
$buildingFilter = isset($_GET['building_filter']) ? $_GET['building_filter'] : '';

// Get list of all unique buildings for dropdown
$buildingList = [];
$buildingQuery = mysqli_query($conn, "SELECT DISTINCT building FROM spaces ORDER BY building ASC");
while ($b = mysqli_fetch_assoc($buildingQuery)) {
    $buildingList[] = $b['building'];
}

// Allowed sort options
$allowedSorts = [
    'id_asc' => 'id ASC',
    'id_desc' => 'id DESC',
    'building_asc' => 'building ASC',
    'building_desc' => 'building DESC',
    'room_asc' => 'room_name ASC',
    'room_desc' => 'room_name DESC',
];

$orderBy = isset($allowedSorts[$sort]) ? $allowedSorts[$sort] : $allowedSorts['id_asc'];

// Build SQL filter clause
$where = [];
if ($search !== '') {
    $escapedSearch = mysqli_real_escape_string($conn, $search);
    $where[] = "(building LIKE '%$escapedSearch%' OR room_name LIKE '%$escapedSearch%')";
}
if ($buildingFilter !== '') {
    $escapedBuilding = mysqli_real_escape_string($conn, $buildingFilter);
    $where[] = "building = '$escapedBuilding'";
}
$filterClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : '';

// Final query
$result = mysqli_query($conn, "SELECT * FROM spaces $filterClause ORDER BY $orderBy");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Spaces</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
    <h1>Space Booking System - Manage Bookable Spaces</h1>
    <nav>
        <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Existing Spaces</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <p class="message">Space deleted successfully.</p>
    <?php elseif (isset($_GET['added'])): ?>
        <p class="message">Space added successfully.</p>
    <?php endif; ?>

    <div class="search-bar">
        <form method="get" class="search-form" style="justify-content: center;">
            <input 
                type="text" 
                name="search" 
                placeholder="Search building or room..." 
                value="<?= htmlspecialchars($search) ?>" 
            />

            <select name="building_filter">
                <option value="">All Buildings</option>
                <?php foreach ($buildingList as $building): ?>
                    <option value="<?= htmlspecialchars($building) ?>" <?= $building === $buildingFilter ? 'selected' : '' ?>>
                        <?= htmlspecialchars($building) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="sort">
                <option value="id_asc" <?= $sort === 'id_asc' ? 'selected' : '' ?>>ID (Ascending)</option>
                <option value="id_desc" <?= $sort === 'id_desc' ? 'selected' : '' ?>>ID (Descending)</option>
                <option value="building_asc" <?= $sort === 'building_asc' ? 'selected' : '' ?>>Building (A–Z)</option>
                <option value="building_desc" <?= $sort === 'building_desc' ? 'selected' : '' ?>>Building (Z–A)</option>
                <option value="room_asc" <?= $sort === 'room_asc' ? 'selected' : '' ?>>Room Name (A–Z)</option>
                <option value="room_desc" <?= $sort === 'room_desc' ? 'selected' : '' ?>>Room Name (Z–A)</option>
            </select>

            <button type="submit" class="btn">Search</button>
            <a href="add_space.php" class="btn nav-btn">Add New Space</a>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 20%;">Building</th>
                <th style="width: 30%;">Room Name</th>
                <th style="width: 20%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="text-align: center;"><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['building']) ?></td>
                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                        <td class="actions" style="text-align: center;">
                            <a href="edit_space.php?id=<?= $row['id'] ?>" class="btn" style="padding:6px 12px; font-size:14px; margin-right: 8px;">Edit</a>
                            <a href="manage_spaces.php?delete=<?= $row['id'] ?>" class="btn delete" style="background-color:#d9534f; padding:6px 12px; font-size:14px;" onclick="return confirm('Are you sure you want to delete this space?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center;">No spaces found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include "footer.php"; ?>

</body>
</html>
