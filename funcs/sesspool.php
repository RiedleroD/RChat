<?php
	class SESS{
		static public $isloggedin = NULL;
		static public $user = NULL;
	}
	include_once("db.php");
	function activate_sesspool(){
		if($_GET["a"]=="logout"){
			include_once("db.php");
			db_end_session($_SESSION["sessid"],$_SESSION["usrid"]);
		}
		if($_GET["s"]=="registered"){
			if($_POST["pass1"]!=$_POST["pass2"]){
				return 0;
			}
			$usr = db_get_user_by_name($_POST["name"]);
			if($usr!=NULL){
				return 1;
			}else if(db_email_taken($_POST["mail"])){
				return 2;
			}else{
				db_new_user($_POST["name"],$_POST["mail"],$_POST["pass1"]);
				header('Location: ./?s=login');
				exit();
			}
		}else if($_GET["s"]=="loggedin"){
			if(db_check_login_usrpwd($_POST["name"],$_POST["pass"])){
				$usr = db_get_user_by_name($_POST["name"]);
				$_SESSION["sessid"]=db_new_session($usr["id"]);
				$_SESSION["usrid"]=$usr["id"];
				header('Location: ./');
				exit();
			}else{
				return 3;
			}
		}
	}
	function genSessionContext(){
		if($_SESSION["sessid"]!=NULL && $_SESSION["usrid"]!=NULL){
			SESS::$isloggedin=db_check_login_session($_SESSION["sessid"],$_SESSION["usrid"]);
			if(SESS::$isloggedin){
				SESS::$user=db_get_user($_SESSION["usrid"]);
			}
		}
	}
	function sesspool_followup($mode){
		switch($mode){
			case 0:
				include "register.php";
				echo "<script>alert('Passwörter stimmen nicht überein')</script>";
				break;
			case 1:
				include "register.php";
				echo "<script>alert('Benutzername nicht verfügbar')</script>";
				break;
			case 2:
				include "register.php";
				echo "<script>alert('Email-Adresse nicht verfügbar')</script>";
				break;
			case 3:
				include "login.php";
				echo "<script>alert('Benutzername oder Passwort falsch!')</script>";
				break;
		}
	}
?>