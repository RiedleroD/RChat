<?php
	if(!SESS::$isloggedin){
		echo "<h4>Melde dich an, um Nachrichten mit anderen auszutauschen.</h4>";
		return;
	}
?>
<div id="content">
	<div id="contacts">
		<?php
			foreach(db_get_contacts(SESS::$user["id"]) as $contact){
				echo "<input type='radio' name='contacts' id='contact${contact['id']}' hidden/>";
				echo "<label for='contact${contact['id']}' onclick='post_receiver=${contact['id']}'><button type='button'>${contact['username']}</button></label>";
			}
		?>
		<form action="#" onsubmit="return false" autocomplete="off" id="contactform">
			<button type="submit" class="elementbtn" id="newcontactbtn">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -1 16 16" width="2em">
					<path fill="currentColor" d="M6 9.5h3v3h6.5A6.5 6.5 0 006 6.73zM9 5a1 1 0 000-6 1 1 0 000 6zM.5 10h3v-3h2v3h3v2h-3v3h-2v-3h-3z"/>
				</svg>
			</button>
			<input type="text" name="name" required placeholder="Username" id="contactin"
			readonly onfocus="this.removeAttribute('readonly');"/><!-- this is shitty, but it's the only way to stop browsers from autofilling this >:( -->
		</form>
	</div>
	<div id="chatcard" class="card" style="visibility:hidden">
		<div id="chatlog" class="card">
			
		</div>
		<form action="#" onsubmit="return false" id="mainform">
			<div id="premsg"></div>
			<button class="elementbtn" type="button" id="new_p">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7 7" width="2em">
					<path fill="currentColor" d="M0 0h4v1H0m0 1h7v1H0m0 1h6v1H0m0 1h2v1H0"/>
				</svg>
			</button>
			<button class="elementbtn" type="button" id="new_h">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 5" width="2em">
					<path fill="currentColor" d="M1 0h1v2h1v-2h1v5h-1v-2h-1v2h-1z"/>
				</svg>
			</button>
			<button class="elementbtn" type="button" id="new_i">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 5" width="2em">
					<path fill="currentColor" d="M0 4.5l2-3 2 3zm4-4a1 1 0 000 2 1 1 0 000-2z"/>
				</svg>
			</button>
			<button class="elementbtn" type="button" id="new_poll">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22" width="2em">
					<path fill="currentColor" d="M4 2a2 2 0 010 4 2 2 0 010-4zm0 1a1 1 0 000 2 1 1 0 000-2zm3 0v2h11v-2zm-3 4a2 2 0 010 4 2 2 0 010-4zm0 1a1 1 0 000 2 1 1 0 000-2zm3 0v2h14v-2zm-3 4a2 2 0 010 4 2 2 0 010-4zm3 1v2h13v-2zm-3 4a2 2 0 010 4 2 2 0 010-4zm0 1a1 1 0 000 2 1 1 0 000-2zm3 0v2h15v-2z"/>
				</svg>
			</button>
			<br/>
			<button type="submit" id="sendbtn">Senden</button>
			<button type="submit" id="previewbtn">Vorschau</button>
		</form>
		<div id="preview">
		</div>
	</div>
	<div id="polls">
		<button type="button" onclick="document.getElementById('pollform').removeAttribute('style');this.setAttribute('style','display:none')">Neue Umfrage</button>
		<form action="#" onsubmit="return false" autocomplete="off" id="pollform" style="visibility:hidden">
			<input type="text" name="title" required placeholder="Titel"/>
			<br/>
			<input type="text" name="answer[0]" required placeholder="Antwort 1"/>
			<button type="button" class="elementbtn" id="addpollanswerbtn">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 5" width="2em">
					<path fill="currentColor" d="M0 2h2v-2h1v2h2v1h-2v2h-1v-2h-2z"/>
				</svg>
			</button>
			<button type="submit" class="elementbtn" id="makepollbtn">Erstellen</button>
		</form>
	</div>
</div>