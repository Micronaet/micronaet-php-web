<?php
if(!session_id()) session_start();

$_SESSION['admin'] = false;
if(isset($_POST['user']) && isset($_POST['password'])){
    $utenti = array();
    
    $utenti[] = array(
	    'user' => 'barisan',
	    'passwd' => 'campeggio',
	    'admin' => false,
    );
    $utenti[] = array(
        'user' => 'arnaud',
        'passwd' => 'Krottev3',
	    'admin' => false,
    );
    $utenti[] = array(
        'user' => 'lanfredi',
        'passwd' => 'relax',
	    'admin' => false,
    );
    $utenti[] = array(
        'user' => 'roberto',
        'passwd' => 'cgp',
	    'admin' => true,
    );
    $utenti[] = array(
        'user' => 'ennio',
        'passwd' => '1956',
	    'admin' => true,
    );
    $utenti[] = array(
        'user' => 'ufficio',
        'passwd' => 'fiam1975',
	    'admin' => false,
    );
    $utenti[] = array(
	    'user' => 'giacomo',
	    'passwd' => 'fiore',
	    'admin' => false,
    );
    $utenti[] = array(
	    'user' => 'greco',
	    'passwd' => 'atene',
	    'admin' => false,
    );
    $utenti[] = array(
	    'user' => 'bussetti',
	    'passwd' => 'cotone',
	    'admin' => false,
    );
    $utenti[] = array(
	    'user' => 'natoli',
	    'passwd' => 'isola',
	    'admin' => false,
    );
    $utenti[] = array(
	    'user' => 'piccolo',
	    'passwd' => 'spiaggia',
	    'admin' => false,
    );
    $utenti[] = array(
	    'user' => 'rosaci',
	    'passwd' => 'piscina',
	    'admin' => false,
    );
    
    foreach($utenti as $u){
	    if($_POST['user'] == $u['user'] && $_POST['password'] == $u['passwd']) {
		    $_SESSION['logged'] = true;		    
		    $_SESSION['admin'] = $u['admin'];
		    die($_SESSION['admin']);
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
