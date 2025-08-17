<?php
session_start();
if (!isset($_SESSION["auth"])){
	echo "<script>window.location.href = 'login.php';</script>";
	exit();
}
if ($_SESSION["auth"] != True){
	echo "<script>window.location.href = 'login.php';</script>";
}
?>
