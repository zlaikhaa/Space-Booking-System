<?php
session_start();
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

include "db.php";

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $id_number = mysqli_real_escape_string($conn, $_SESSION['id_number']);
    $delete_id = intval($_POST['delete_booking_id']); // sanitize as integer

    // Delete booking only if it belongs to the logged-in user
    $deleteQuery = "
        DELETE FROM bookings
        WHERE id = $delete_id
          AND user_id_number = '$id_number'
        LIMIT 1
    ";

    if (mysqli_query($conn, $deleteQuery)) {
        if (mysqli_affected_rows($conn) > 0) {
            $_SESSION['msg'] = "Booking deleted successfully.";
        } else {
            $_SESSION['msg'] = "Booking not found or you don't have permission to delete it.";
        }
    } else {
        $_SESSION['msg'] = "Error deleting booking: " . mysqli_error($conn);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$id_number = mysqli_real_escape_string($conn, $_SESSION['id_number']);

// Get building list from user's bookings (to populate filter dropdown)
$buildingResult = mysqli_query($conn, "
    SELECT DISTINCT s.building 
    FROM bookings b
    LEFT JOIN spaces s ON b.room = s.room_name
    WHERE b.user_id_number = '$id_number'
    ORDER BY s.building ASC
");
$buildings = [];
while ($b = mysqli_fetch_assoc($buildingResult)) {
    if (!empty($b['building'])) {
        $buildings[] = $b['building'];
    }
}

// Filters and sorting from GET
$filterBuilding = isset($_GET['filter_building']) ? $_GET['filter_building'] : '';
$filterStatus = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

$whereParts = ["b.user_id_number = '$id_number'"];

if ($filterBuilding !== '' && in_array($filterBuilding, $buildings)) {
    $escapedBuilding = mysqli_real_escape_string($conn, $filterBuilding);
    $whereParts[] = "s.building = '$escapedBuilding'";
}

if (in_array($filterStatus, ['Pending', 'Approved', 'Rejected'])) {
    $escapedStatus = mysqli_real_escape_string($conn, $filterStatus);
    $whereParts[] = "b.status = '$escapedStatus'";
}

$whereSQL = count($whereParts) > 0 ? 'WHERE ' . implode(' AND ', $whereParts) : '';

if ($sort === 'date_asc') {
    $orderBy = "b.date ASC, b.time ASC";
} elseif ($sort === 'date_desc') {
    $orderBy = "b.date DESC, b.time DESC";
} elseif ($sort === 'status_asc') {
    $orderBy = "b.status ASC";
} elseif ($sort === 'status_desc') {
    $orderBy = "b.status DESC";
} else {
    $orderBy = "b.date DESC, b.time DESC";
}

$query = "
    SELECT b.id, b.room, b.date, b.time, b.status, s.building
    FROM bookings b
    LEFT JOIN spaces s ON b.room = s.room_name
    $whereSQL
    ORDER BY $orderBy
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Bookings</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
    <h1>Space Booking System - My Bookings</h1>
    <nav>
        <a href="lecturer_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Booking List</h2>

    <?php if ($msg): ?>
        <script>alert(<?= json_encode($msg) ?>);</script>
    <?php endif; ?>

    <!-- Filter Form -->
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
                <th>No.</th>
                <th>Room</th>
                <th>Building</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th> <!-- Delete column -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['room']) ?></td>
                        <td><?= htmlspecialchars($row['building'] ?: 'Unknown') ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form method="post" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                <input type="hidden" name="delete_booking_id" value="<?= (int)$row['id'] ?>" />
                                <button type="submit" class="btn-delete" style="background-color:#d9534f;">Delete</button>
                            </form>
                        </td>
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
