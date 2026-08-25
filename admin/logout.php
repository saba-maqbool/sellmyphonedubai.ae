<?php
include("include/db-connect.php");
include("include/auth-check.php");
?>
<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>