<?php
	require_once("json.php");
	$json = new json();
	//error_reporting(0);

	###### Constants ######
	
	/* Define Constants */
	define("HOST", "localhost");
	
	/* User for MYSQL */
	define("USER", "capstoneAdmin");
	
	/* Password for MYSQL */
	define("PASS", "Capstone2014");
	
	/* Database for MYSQL */
	define("DBASE", "capstone");
	
	/* Debug Mode Info */
	define("MODE", 0);
	
	###### Global Variables ######

	/* Global MYSQL Object */
	$db = new PDO('mysql:host='.HOST.';dbname='.DBASE.';charset=utf8', USER, PASS);
	
	function requireSSL(){
		if($_SERVER["HTTPS"] != "on")
		{
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
			exit();
		}
	}
?>