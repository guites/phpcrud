<?php
session_start();

$_SESSION = array();

session_destroy();
session_start();

$_SESSION["logout"] = true;

header("location: login.php");
exit;
