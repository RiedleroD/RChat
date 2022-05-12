<?php
	function _db_connect(){
		$db = new PDO("mysql:host=192.168.43.52;dbname=6m1_2","tgm");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	}
	function _db_get_pq($db,$query,$args){
		$stmt = $db->prepare($query);
		$stmt->execute($args);
		return $stmt;
	}
	function _hash_passwd($passwd){
		return hash('whirlpool',"riedler.wien".$passwd,true);
	}
	function db_add_msg($author,$receiver,$content){
		$db = _db_connect();
		$db->prepare("INSERT INTO posts VALUES (0,?,NOW(),?)")->execute([$author,$receiver]);
		$i = (int)($db->lastInsertId());
		$j = 1;
		$x = 0;
		foreach($content as $stuff){
			$typenum = array_search($stuff["type"],array("txt","h","img","poll"));
			$db->prepare("INSERT INTO elements VALUES (0,?,?,?)")->execute([$i,$j,$typenum]);
			$x = (int)($db->lastInsertId());
			switch($stuff["type"]){
				case "txt":
					$db->prepare("INSERT INTO paragraphs VALUES (?,?)")->execute([$x,$stuff['txt']]);
					break;
				case "h":
					$db->prepare("INSERT INTO headings VALUES (?,?)")->execute([$x,$stuff['txt']]);
					break;
				case "img":
					$db->prepare("INSERT INTO images VALUES (?,?,?)")->execute([$x,$stuff['src'],$stuff['alt']]);
					break;
				case "poll":
					$db->prepare("INSERT INTO psends VALUES (?,?)")->execute([$x,$stuff['pollid']]);
					break;
			}
			$j+=1;
		}
	}
	function db_get_msg($msgid){
		$db = _db_connect();
		$msg = _db_get_pq($db,"SELECT id,author,date,receiver FROM posts WHERE id=?",[$msgid])->fetch();
		$msg["elements"]=db_get_elements_from_msg($db,$msgid);
		return $msg;
	}
	function db_get_elements_from_msg($db,$id){
		$elements = array();
		foreach($row=$db->query("SELECT id,type FROM elements WHERE post=$id ORDER BY element ASC") as $row){
			$eid=(int)($row["id"]);
			$data = array();
			switch((int)($row["type"])){
				case 0:
					$data["type"]="txt";
					$data["txt"] = _db_get_pq($db,"SELECT text FROM paragraphs WHERE id=?",[$eid])->fetch()["text"];
					break;
				case 1:
					$data["type"]="h";
					$data["txt"] = _db_get_pq($db,"SELECT text FROM headings WHERE id=?",[$eid])->fetch()["text"];
					break;
				case 2:
					$data["type"]="img";
					$img = _db_get_pq($db,"SELECT src,alt FROM images WHERE id=?",[$eid])->fetch();
					$data["src"] = $img["src"];
					$data["alt"] = $img["alt"];
					break;
				case 3:
					$data["type"]="poll";
					$data["pollid"]=(int)(_db_get_pq($db,"SELECT pollid FROM psends WHERE id=?",[$eid])->fetch()["pollid"]);
					$data["namenr"]=$eid;
					$data["title"]=_db_get_pq($db,"SELECT title FROM polls WHERE id=?",[$data["pollid"]])->fetch()["title"];
					$data["answers"]=array();
					foreach(_db_get_pq($db,"SELECT id,answer FROM panswers WHERE poll=?",[$data["pollid"]])->fetchAll() as $poll){
						$data["answers"][(int)($poll["id"])]=$poll["answer"];
					}
					break;
				default:
					continue 2;//continues in the second level, skipping the switch and going to the foreach
			}
			array_push($elements,$data);
		}
		return $elements;
	}
	function db_get_user_by_name($name){
		$db = _db_connect();
		return _db_get_pq($db,"SELECT id,username,email,passwd FROM users WHERE username=? LIMIT 1",[$name])->fetch();
	}
	function db_get_user($usrid){
		$db = _db_connect();
		return _db_get_pq($db,"SELECT id,username,email,passwd FROM users WHERE id=?",[$usrid])->fetch();
	}
	function db_get_contacts($usrid){
		$db = _db_connect();
		//what a query
		return _db_get_pq($db,"SELECT DISTINCT users.id,username,email FROM users INNER JOIN posts ON posts.receiver=users.id OR posts.author=users.id WHERE (posts.author=? OR posts.receiver=?) AND NOT users.id=?",[$usrid,$usrid,$usrid])->fetchAll();
	}
	function db_email_taken($email){
		$db = _db_connect();
		return _db_get_pq($db,"SELECT id FROM users WHERE email=?",[$email])->fetch()!=NULL;
	}
	function db_new_user($name,$email,$passwd){
		$db = _db_connect();
		$db->prepare("INSERT INTO users VALUES (0,?,?,?)")->execute([$name,$email,_hash_passwd($passwd)]);
	}
	function db_check_login_usrpwd($name,$passwd){
		$db = _db_connect();
		return _db_get_pq($db,"SELECT id FROM users WHERE username=? AND passwd=?",[$name,_hash_passwd($passwd)])->fetch()!=NULL;
	}
	function db_check_login_session($sessid,$usrid){
		$db = _db_connect();
		return _db_get_pq($db,"SELECT id FROM sessions WHERE id=? AND user=?",[$sessid,$usrid])->fetch()!=NULL;
	}
	function db_new_session($usrid){
		$db = _db_connect();
		$db->prepare("INSERT INTO sessions VALUES (0,?,DATE_ADD(NOW(),INTERVAL 1 DAY))")->execute([$usrid]);
		return (int)($db->lastInsertId());
	}
	function db_end_session($sessid,$usrid){
		$db = _db_connect();
		$db->prepare("DELETE FROM sessions WHERE id=? AND user=?")->execute([$sessid,$usrid]);
	}
	function db_get_chat_since($author,$receiver,$lastknownmsg){
		$db = _db_connect();
		$chat = _db_get_pq($db,"SELECT DISTINCT id FROM posts WHERE id>? AND ((receiver=? AND author=?) OR (receiver=? AND author=?))",[$lastknownmsg,$receiver,$author,$author,$receiver])->fetchAll();
		if($chat==null)
			return array();
		else
			return $chat;
	}
	function db_new_poll($userid,$title,$answers){
		$db = _db_connect();
		$db->prepare("INSERT INTO polls VALUES (0,?,?)")->execute([$userid,$title]);
		$pollid = (int)($db->lastInsertId());
		foreach($answers as $answer){
			$db->prepare("INSERT INTO panswers VALUES (0,?,?)")->execute([$pollid,$answer]);
		}
	}
	function db_get_users_polls($userid){
		$db = _db_connect();
		$result = array();
		$values = _db_get_pq($db,"SELECT id,title FROM polls WHERE author=?",[$userid])->fetchAll();
		foreach($values as $value){
			$result[(int)($value["id"])]=$value["title"];
		}
		return $result;
	}
?>