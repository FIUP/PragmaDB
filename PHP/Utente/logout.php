<?php

require('../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(!empty($_SESSION['user'])){
	$sname=session_name();
	$_SESSION = [];
	session_destroy();
	if(isset($_COOKIE[$sname])){
		setcookie($sname,'',time()-3600,'/');
	}
}

header("Location: $absurl/index.php");

?>
