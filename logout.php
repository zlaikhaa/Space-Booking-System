<?php
session_start();
session_destroy();

// Clear cookies
setcookie('remembered_user', '', time() - 3600, "/");
setcookie('remembered_pass', '', time() - 3600, "/");

header("Location: login.php");
exit();
?>