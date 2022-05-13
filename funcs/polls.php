<div class="card">
	<div class="cardheader"><span>Poll-header</span></div>
	<svg viewbox="0 0 13 3.8" width="11em">
		<path d="M0 0.2v1h2v-1z" fill="#926"/>
		<text y="0.7">2<tspan x="13">Ein Testtitel</tspan></text>
		<path d="M0 1.4v1h1v-1z" fill="#4CC"/>
		<text y="1.9">2<tspan x="13">A Testtitel</tspan></text>
		<path d="M0 2.6v1h3v-1z" fill="#037"/>
		<text y="3.1">3<tspan x="13">Meem</tspan></text>
	</svg>
</div>
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
<div class="card">
	<div class="cardheader"><span>Poll-header</span></div>
	<svg viewbox="0 0 13 3.8" width="11em">
		<path d="M0 0.2v1h2v-1z" fill="#926"/>
		<text y="0.7">2<tspan x="13">Ein Testtitel</tspan></text>
		<path d="M0 1.4v1h1v-1z" fill="#4CC"/>
		<text y="1.9">2<tspan x="13">A Testtitel</tspan></text>
		<path d="M0 2.6v1h3v-1z" fill="#037"/>
		<text y="3.1">3<tspan x="13">Meem</tspan></text>
	</svg>
</div>