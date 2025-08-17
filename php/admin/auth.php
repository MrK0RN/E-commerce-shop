<?php
session_start();
if ($_SESSION["auth"] != True){
	header("Location: login.php");
}
?>