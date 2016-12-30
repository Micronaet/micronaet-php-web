<?php
if(!session_id()) session_start();

if(isset($_POST['user']) && isset($_POST['password'])){
    $utenti = array();
    $admin = array('roberto', 'ennio', 'ufficio');
    
    $utenti[] = array(
	    'user' => 'barisan',
	    'passwd' => 'campeggio',
    );
    $utenti[] = array(
        'user' => 'arnaud',
        'passwd' => 'Krottev3',
    );
    $utenti[] = array(
        'user' => 'lanfredi',
        'passwd' => 'relax',
    );
    $utenti[] = array(
        'user' => 'roberto',
        'passwd' => 'cgp',
    );
    $utenti[] = array(
        'user' => 'ennio',
        'passwd' => '1956',
    );
    $utenti[] = array(
        'user' => 'ufficio',
        'passwd' => 'fiam1975',
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
	    'user' => 'piccolo',
	    'passwd' => 'spiaggia',
    );
    $utenti[] = array(
	    'user' => 'rosaci',
	    'passwd' => 'piscina',
    );

    foreach($utenti as $u){
	    if($_POST['user'] == $u['user'] && $_POST['password'] == $u['passwd']) {
		    $_SESSION['logged'] = true;
		    if (in_array($_POST['user'], $admin) {
		        $_SESSION['admin'] = true;		        
		        }
		    else {
		        $_SESSION['admin'] = false;
		        }    
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
