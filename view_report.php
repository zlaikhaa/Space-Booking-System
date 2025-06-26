<?php
session_start();
if (!isset($_SESSION['id_number']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get building list for filter dropdown
$buildingResult = mysqli_query($conn, "SELECT DISTINCT building FROM spaces ORDER BY building ASC");
$buildings = [];
while ($b = mysqli_fetch_assoc($buildingResult)) {
    $buildings[] = $b['building'];
}

// Filters and sorting from GET
$filterBuilding = isset($_GET['filter_building']) ? $_GET['filter_building'] : '';
$filterStatus = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

$whereParts = [];
if ($filterBuilding !== '' && in_array($filterBuilding, $buildings)) {
    $escapedBuilding = mysqli_real_escape_string($conn, $filterBuilding);
    $whereParts[] = "s.building = '$escapedBuilding'";
}
if (in_array($filterStatus, ['Pending', 'Approved', 'Rejected'])) {
    $escapedStatus = mysqli_real_escape_string($conn, $filterStatus);
    $whereParts[] = "b.status = '$escapedStatus'";
}

$whereSQL = count($whereParts) > 0 ? 'WHERE ' . implode(' AND ', $whereParts) : '';

$allowedSorts = [
    'date_asc' => 'b.date ASC, b.time ASC',
    'date_desc' => 'b.date DESC, b.time DESC',
    'status_asc' => 'b.status ASC',
    'status_desc' => 'b.status DESC'
];
$orderBy = isset($allowedSorts[$sort]) ? $allowedSorts[$sort] : $allowedSorts['date_desc'];

$query = "
    SELECT b.id, u.username, b.room, b.date, b.time, b.status, s.building
    FROM bookings b
    JOIN users u ON b.user_id_number = u.id_number
    JOIN spaces s ON b.room = s.room_name
    $whereSQL
    ORDER BY $orderBy
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Booking Reports</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
    <h1>Space Booking System - Booking Report</h1>
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
    <h2>Booking Report</h2>

    <!-- Search and Filter Form -->
    <div class="search-bar">
        <form method="get" class="search-form">
            <select name="filter_building">
                <option value="" <?= $filterBuilding === '' ? 'selected' : '' ?>>All Buildings</option>
                <?php foreach ($buildings as $b): ?>
                    <option value="<?= htmlspecialchars($b) ?>" <?= $filterBuilding === $b ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="filter_status">
                <option value="" <?= $filterStatus === '' ? 'selected' : '' ?>>All Statuses</option>
                <?php foreach (['Pending', 'Approved', 'Rejected'] as $status): ?>
                    <option value="<?= $status ?>" <?= $filterStatus === $status ? 'selected' : '' ?>>
                        <?= $status ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="sort">
                <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Date (Newest)</option>
                <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Date (Oldest)</option>
                <option value="status_asc" <?= $sort === 'status_asc' ? 'selected' : '' ?>>Status (A–Z)</option>
                <option value="status_desc" <?= $sort === 'status_desc' ? 'selected' : '' ?>>Status (Z–A)</option>
            </select>

            <button type="submit" class="btn">Apply</button>
        </form>
    </div>

    <!-- Bookings Table -->
    <table class="admin-table-booking">
        <thead>
            <tr>
                <th>ID</th>
                <th>Lecturer</th>
                <th>Room</th>
                <th>Building</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['room']) ?></td>
                        <td><?= htmlspecialchars($row['building']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button class="print-btn" onclick="window.print()">Print Report 📩</button>
</main>

<?php include "footer.php"; ?>

</body>
</html>
