<?php
session_start();
if (!isset($_SESSION['id_number']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

include "db.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_SESSION['id_number'];

    // Sanitize inputs
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $room = mysqli_real_escape_string($conn, $_POST['room']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);

    $today = date('Y-m-d');

    // Prevent booking for past dates
    if ($date < $today) {
        $msg = "You cannot book a room for a past date.";
    } else {
        // Check if room is already booked for the selected date and time
        $check = mysqli_query($conn, "
            SELECT * FROM bookings 
            WHERE room='$room' 
            AND date='$date' 
            AND time='$time' 
            AND status IN ('Pending', 'Accepted')
        ");

        if (mysqli_num_rows($check) > 0) {
            $msg = "This room is unavailable at the selected date and time.";
        } else {
            // Insert booking
            $insert = "INSERT INTO bookings (user_id_number, room, date, time, status) 
                       VALUES ('$id_number', '$room', '$date', '$time', 'Pending')";
            if (mysqli_query($conn, $insert)) {
                $msg = "Booking submitted successfully. <a href='view_booking.php'>View your bookings</a>.";
            } else {
                $msg = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch all spaces grouped by building
$spaces_result = mysqli_query($conn, "SELECT * FROM spaces ORDER BY building, room_name");
$spaces = [];
while ($row = mysqli_fetch_assoc($spaces_result)) {
    $spaces[$row['building']][] = $row['room_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book a Room</title>
    <link rel="stylesheet" href="style.css">
    <script>
        const spaces = <?= json_encode($spaces); ?>;
        function updateRooms() {
            const building = document.getElementById('building').value;
            const roomSelect = document.getElementById('room');
            roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
            if (spaces[building]) {
                spaces[building].forEach(room => {
                    const option = document.createElement('option');
                    option.value = room;
                    option.textContent = room;
                    roomSelect.appendChild(option);
                });
            }
        }
    </script>
</head>
<body>

<header>
    <h1>Space Booking System - Book Room</h1>
    <nav>
        <a href="lecturer_dashboard.php">⬅ Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="form-container">
    <h2>Book a Room</h2>

    <?php if ($msg): ?>
        <p class="message"><?= $msg ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="building">Building</label>
            <select name="building" id="building" required onchange="updateRooms()">
                <option value="">-- Select Building --</option>
                <?php foreach ($spaces as $building => $rooms): ?>
                    <option value="<?= htmlspecialchars($building) ?>"><?= htmlspecialchars($building) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="room">Room</label>
            <select name="room" id="room" required>
                <option value="">-- Select Room --</option>
            </select>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" name="date" id="date" required min="<?= date('Y-m-d') ?>">
        </div>

        <div class="form-group">
            <label for="time">Time</label>
            <input type="time" name="time" id="time" required>
        </div>

        <button type="submit">Submit Booking</button>
    </form>
</main>

<?php include "footer.php"; ?>
</body>
</html>
