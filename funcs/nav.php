<nav>
	<a href="./">Hauptseite</a>
	<a href="./">Umfragen</a>
	<span id="logreg">
		<?php
			include_once("db.php");
			if(SESS::$isloggedin){
				$usrname = htmlentities(SESS::$user["username"]);
				$userid = SESS::$user['id'];
				echo "<a href='./?a=logout' id='logout_btn' data-userid='$userid'>Logged in as $usrname</a>";
			}else{
				echo "<a href='./?s=login'>Einloggen</a><a href='./?s=register'>Registrieren</a>";
			}
		?>
	</span>
</nav>