<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>TSU 1DB</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>TSU 1DB : หน่วยเฝ้าตรวจ ขกท.สน.จชต.</h1>
				<a href="people.php"><i class="fas fa-user-circle"></i>People</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>TSU 1DB</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
			
		</div>
	</body>
</html>
