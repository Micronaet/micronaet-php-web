<?php
if(!session_id()) session_start();

if(isset($_POST['user']) && isset($_POST['password'])){

$utenti = array();
$utenti[] = array(
	'user' => 'barisan',
	'passwd' => 'campeggio',
);
$utenti[] = array(
        'user' => 'arnaud',
        'passwd' => 'Krottev3',
);
$utenti[] = array(
        'user' => 'lanfredi1',
        'passwd' => 'relax',
);

$utenti[] = array(
	'user' => 'giacomo',
	'passwd' => 'fiore',
);
$utenti[] = array(
	'user' => 'greco',
	'passwd' => 'atene',
);
$utenti[] = array(
	'user' => 'bussetti',
	'passwd' => 'cotone',
);
$utenti[] = array(
	'user' => 'natoli',
	'passwd' => 'isola',
);
$utenti[] = array(
	'user' => 'paladini',
	'passwd' => 'giardino',
);
$utenti[] = array(
	'user' => 'piccolo',
	'passwd' => 'spiaggia',
);
$utenti[] = array(
	'user' => 'rosaci',
	'passwd' => 'piscina',
);

	foreach($utenti as $u){
		if($_POST['user'] == $u['user'] && $_POST['password'] == $u['passwd']){
			$_SESSION['logged'] = true;

			header('Location: find.php');
			die;

		}
	}	


}

echo <<<OUT
<html>
<head></head>
<body>
<form method="post" action="loginmic.php">
<input type="text" name="user" placeholder="nome utente" />
<input type="password" placeholder="password" name="password" />
<input type="submit" />
</form>

</body>
</html>


OUT;

?>
