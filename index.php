<?php
	session_start();
	include_once("./funcs/sesspool.php");
	$sess_mode = activate_sesspool();//pass 1 - auth & db stuff & http redirects
	genSessionContext();//pass 2 - filling out SESS singleton
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8"/>
		<title>Riedler's Chat</title>
		<link rel="icon" type="image/svg" href="/favicon.svg"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!--TODO: uncomment before handing in
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>-->
		<link rel="stylesheet" href="./style/main.css"/>
		<?php
		switch($_GET["s"]){
			case "register":
			case "login":
			case "loggedin":
			case "registered":
				break;
			case "polls":
				echo "<link rel='stylesheet' href='./style/polls.css'/>";
				break;
			default:
				echo "<script async src='./scripts/chat.js'></script>";
				echo "<link rel='stylesheet' href='./style/chat.css'/>";
		}
		?>
	</head>
	<body>
		<?php
			include "./funcs/nav.php";
			switch($_GET["s"]){
				case "register":
					include "./funcs/register.php";
					break;
				case "login":
					include "./funcs/login.php";
					break;
				case "loggedin":
				case "registered":
					sesspool_followup($sess_mode);//pass 3 - warns & pseudo-redirects
					break;
				case "polls":
					include "./funcs/polls.php";
					break;
				default:
					include "./funcs/chat.php";
			}
		?>
	</body>
</html>