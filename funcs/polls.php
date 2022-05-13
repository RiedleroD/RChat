<?php
	if(SESS::$isloggedin){
		foreach(db_get_users_polls_full(SESS::$user["id"]) as $pid=>$poll){
			echo "<div class='card'>";
			echo "<div class='cardheader'><span>${poll['title']}</span></div>";
			$answers = $poll["answers"];
			$maxvotes=0;
			$toprint = array();
			foreach($answers as $aid=>$answer){
				if($answer['votes']>$maxvotes)
					$maxvotes=$answer['votes'];
			}
			echo "<svg viewbox='0 0 ".($maxvotes+10)." ".(count($answers)*1.2)."' width='".($maxvotes+10)."em'>";
			$yoffset = -1;
			foreach($answers as $aid=>$answer){
				$yoffset += 1.2;
				//TODO: pseudo-random fill color dependent on $aid
				echo "<path d='M0 {$yoffset}v1h{$answer['votes']}v-1z' fill='#926'/>";
				echo "<text x='0.25' y='".($yoffset+0.5)."'>{$answer['votes']}<tspan x='".($maxvotes+10)."'>{$answer['answer']}</tspan></text>";
			}
			echo "</svg></div>";
		}
	}else{
		echo "<span>Log in to view your polls</span>";
	}
?>