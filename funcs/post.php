<?php
include_once("sesspool.php");
include_once("db.php");
session_start();
genSessionContext();
function genMessage($datetime,$content,$side){
	switch($side){
		case false:
			$side=" other";
			break;
		case true:
			$side=" own";
			break;
		case 3:
			$side="";
			break;
	}
	echo "<div class='post$side'>";
	foreach($content as $stuff){
		switch($stuff["type"]){
			case "txt":
				echo "<p>";
				echo htmlentities($stuff['txt']);
				echo "</p>";
				break;
			case "h":
				echo "<h3>";
				echo htmlentities($stuff['txt']);
				echo "</h3>";
				break;
			case "img":
				$alt = htmlentities($stuff['alt']);
				echo "<div class='bpimgcontainer'><img src='";
				echo htmlentities($stuff['src']);
				echo "' alt='$alt'><span>$alt</span></div>";
				break;
			default:
				echo "<p>Unrecognized content type: ${stuff['type']}</p>";
		}
	}
	echo "<div class='cardfooter posttime'><span>$datetime</span></div>";
	echo "</div>";
}
if($_GET["s"]=="send"){
	if(SESS::$isloggedin){
		db_add_msg(SESS::$user["id"],$_POST["receiver"],$_POST["element"]);
	}else{
		echo "not logged in";
	}
}else if($_GET["s"]=="preview"){
	genMessage("now",$_POST["element"],3);
}else if($_GET["s"]=="genmsg"){
	$msg = db_get_msg($_POST["msgid"]);
	genMessage($msg["date"],$msg["elements"],$msg["author"]==SESS::$user["id"]);
}else if($_GET["s"]=="fetch"){
	echo '[';
	$notfirst=false;
	foreach(db_get_chat_since(SESS::$user["id"],$_POST["receiver"],$_POST["lastknownmessage"]) as $postid){
		if($notfirst)
			echo ',';
		echo $postid["id"];
		$notfirst=true;
	}
	echo ']';
}else if($_GET["s"]=="contact"){
	//php converts true to 'true' and false to '' for no fucking reason
	$user = db_get_user_by_name($_POST["name"]);
	if($user!=NULL)
		echo $user["id"];
	else
		echo "null";
}
?>