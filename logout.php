<?php
session_start();
session_unset();
session_destroy();
header("Location: /ease-meds/login.php");
exit();
?>