<?php
if(!session_id()) session_start();

if(isset($_POST['user']) && isset($_POST['password'])){
    $utenti = array();

    $utenti[] = array(
	    'user' => 'eventi', 'passwd' => 'itneve157!', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'tisti', 'passwd' => 'itsit357!', 'root' => false,
    );
    $utenti[] = array(
        'user' => 'lanfredi', 'passwd' => 'relax', 'root' => false,
    );
    $utenti[] = array(
        'user' => 'paola', 'passwd' => 'p1l@tes', 'root' => true,
    );
    $utenti[] = array(
        'user' => 'roberto', 'passwd' => 'cgp', 'root' => true,
    );
    $utenti[] = array(
        'user' => 'ennio', 'passwd' => '1956', 'root' => true,
    );
    $utenti[] = array(
        'user' => 'ufficio', 'passwd' => 'fiam1975', 'root' => true,
    );
    $utenti[] = array(
	    'user' => 'bussetti', 'passwd' => 'cotone', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'luigi', 'passwd' => 'DF@Sbbe#cot', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'natoli', 'passwd' => 'isola', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'piccolo', 'passwd' => 'spiaggia', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'rosaci', 'passwd' => 'piscina', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'hanne', 'passwd' => 'halo', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'dispo', 'passwd' => 'fiamgpb', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'gwm', 'passwd' => '#GWM753@', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'robin', 'passwd' => '#Ober753@', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'hanne', 'passwd' => 'HCebbesen', 'root' => false,
    );
    $utenti[] = array(
	    'user' => 'spain', 'passwd' => 'Rfts@45H', 'root' => false,
    );
    $_SESSION['root'] = true;
    foreach($utenti as $u){
	    if($_POST['user'] == $u['user'] && $_POST['password'] == $u['passwd']) {
		    $_SESSION['logged'] = true;
		    $_SESSION['root'] = $u['root'];
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
