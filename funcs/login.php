<form class="card" action="./?s=loggedin" method="post">
	<div class='cardheader'>
		<span>Einloggen</span>
	</div>
	<input id='in_name' name="name" type='text' value='<?=$_POST["name"]?>' required placeholder='Benutzername' autocomplete="username"/>
	<br/>
	<input id='in_pass' name="pass" type='password' value='<?=$_POST["pass"]?>' required placeholder='Passwort'/>
	<br/>
	<br/>
	<button type='submit'>Einloggen</button>
</form>