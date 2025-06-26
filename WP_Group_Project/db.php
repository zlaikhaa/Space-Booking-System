<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "space_booking";

$conn = mysqli_connect($host, $user, $pass, $dbname) or die;

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
