<form class="card" action="./?s=registered" method="post">
	<div class='cardheader'>
		<span>Registrierung</span>
	</div>
	<input id='in_name' name="name" type='text' value='<?=$_POST["name"]?>' required placeholder='Benutzername' autocomplete="username"/>
	<br/>
	<input id='in_mail' name="mail" type='email' value='<?=$_POST["mail"]?>' required placeholder='Email-Adresse'/>
	<br/>
	<input id='in_pass1' name="pass1" type='password' value='<?=$_POST["pass1"]?>' required placeholder='Passwort'/>
	<input id='in_pass2' name="pass2" type='password' value='' required placeholder='Passwort wiederholen'/>
	<!-- TODO: check that pass1 and pass2 are the same -->
	<br/>
	<br/>
	<button type='submit'>Registrieren</button>
</form>