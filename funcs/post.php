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
			case "poll":
				//TODO: check if person has voted
				echo "<h4>${stuff['title']}</h4>";
				foreach($stuff['answers'] as $aid=>$atext){
					echo "<input type='radio' name='${stuff['namenr']}poll${stuff['id']}' id='${stuff['namenr']}poll${stuff['id']}_$aid'/>";
					echo "<label class='polllabel' for='${stuff['namenr']}poll${stuff['id']}_$aid'>$atext</label>";
				}
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
		if($_POST["element"]){
			db_add_msg(SESS::$user["id"],$_POST["receiver"],$_POST["element"]);
		}else{
			echo "empty messages are not allowed";
		}
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
}else if($_GET["s"]=="newpoll"){
	if(SESS::$isloggedin){
		db_new_poll(SESS::$user["id"],$_POST["title"],$_POST["answer"]);
	}else{
		echo "not logged in";
		 http_response_code(403);
	}
}else if($_GET["s"]=="getpolls"){
	if(SESS::$isloggedin){
		$notfirst=false;
		echo '{';
		foreach(db_get_users_polls(SESS::$user["id"]) as $pollid=>$title){
			if($notfirst)
				echo ',';
			else
				$notfirst=true;
			echo '"';
			echo $pollid;
			echo '":"';
			echo addslashes($title);
			echo '"';
		}
		echo '}';
	}else{
		echo "not logged in";
		http_response_code(403);
	}
}
?>