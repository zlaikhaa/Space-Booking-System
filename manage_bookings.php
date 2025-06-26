<?php
session_start();
include "db.php";

if (!isset($_SESSION['id_number']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE bookings SET status = '$status' WHERE id = $booking_id");
}

$buildingResult = mysqli_query($conn, "SELECT DISTINCT building FROM spaces ORDER BY building ASC");
$buildings = [];
while ($b = mysqli_fetch_assoc($buildingResult)) {
    $buildings[] = $b['building'];
}

// Search and sort variables for bookings
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterBuilding = isset($_GET['filter_building']) ? $_GET['filter_building'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_asc';
$whereParts = [];

// Prepare WHERE parts for search and filter
if ($search !== '') {
    $escapedSearch = mysqli_real_escape_string($conn, $search);
    $whereParts[] = "(spaces.building LIKE '%$escapedSearch%' OR bookings.room LIKE '%$escapedSearch%')";
}

if ($filterBuilding !== '' && in_array($filterBuilding, $buildings)) {
    $escapedBuilding = mysqli_real_escape_string($conn, $filterBuilding);
    $whereParts[] = "spaces.building = '$escapedBuilding'";
}

$whereSQL = '';
if (count($whereParts) > 0) {
    $whereSQL = "WHERE " . implode(' AND ', $whereParts);
}

// Allowed sorts
$allowedSorts = [
    'id_asc' => 'bookings.id ASC',
    'id_desc' => 'bookings.id DESC',
    'building_asc' => 'spaces.building ASC',
    'building_desc' => 'spaces.building DESC',
    'room_asc' => 'bookings.room ASC',
    'room_desc' => 'bookings.room DESC',
];

$orderBy = isset($allowedSorts[$sort]) ? $allowedSorts[$sort] : $allowedSorts['id_asc'];

// Final query
$result = mysqli_query($conn, "
    SELECT 
        bookings.id, 
        users.username, 
        bookings.room, 
        bookings.date, 
        bookings.time, 
        bookings.status, 
        spaces.building
    FROM bookings
    JOIN users ON bookings.user_id_number = users.id_number
    JOIN spaces ON bookings.room = spaces.room_name
    $whereSQL
    ORDER BY $orderBy
");

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Space Booking System - Manage Bookings</h1>
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
    <h2>Booking List</h2>

    <!-- Search and Sort Form -->
    <div class="search-bar">
        <form method="get" class="search-form">
            <input 
                type="text" 
                name="search" 
                placeholder="Search building or room..." 
                value="<?= htmlspecialchars($search) ?>"
            >

            <select name="filter_building">
                <option value="" <?= $filterBuilding === '' ? 'selected' : '' ?>>All Buildings</option>
                <?php foreach ($buildings as $buildingOption): ?>
                    <option value="<?= htmlspecialchars($buildingOption) ?>" <?= $filterBuilding === $buildingOption ? 'selected' : '' ?>>
                        <?= htmlspecialchars($buildingOption) ?>
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
        </form>
    </div>


    <!-- Bookings Table -->
    <table class="admin-table-booking">
        <thead>
            <tr>
                <th>Lecturer</th>
                <th>Room</th>
                <th>Building</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['room']) ?></td>
                        <td><?= htmlspecialchars($row['building']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td class="action">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                <?php if ($row['status'] !== 'Approved'): ?>
                                    <button name="status" value="Approved" class="btn">Approve</button>
                                <?php endif; ?>

                                <?php if ($row['status'] !== 'Rejected'): ?>
                                    <button name="status" value="Rejected" class="btn" style="background-color:#d9534f;">Reject</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include "footer.php"; ?>

</body>
</html>
