<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <?php
    if(!session_id()) session_start();

    //verifico se la richiesta è nella lan locale
    $ips = array("91.187.199.104", "91.187.199.105", "91.187.199.106", "91.187.199.107", "79.60.135.198");

    $location = '-';
    if(!isset($_SESSION['logged'])){
        if(!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
            $location = 'FUORI SEDE';
            $_SESSION['location'] = $location;
            header('Location: loginmic.php');
            die;
            }
        else {
            $_SESSION['logged'] = true;
            $location = 'IN SEDE';
            $_SESSION['location'] = $location;
            }
        }
    elseif (isset($_SESSION['location'])) {
        $location = $_SESSION['location'];
        }

    function getBrowser(){   
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/iPhone/i',$u_agent)){
            $bname = 'iPhone';
            $ub = "IPHONE";
            }
        elseif (preg_match('/BlackBerry/i',$u_agent)){
            $bname = 'BlackBerry';
            $ub = "BB";
            }
        else {  
            $bname = 'Browser';
            $ub = "BROWSER";
            }

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'sigla'     => $ub,
            );
        }

    $ua = getBrowser();
    $yourbrowser = $ua['sigla'];
    $company1 = 'fia';
    $company2 = 'gpb';
    
    if (isset($_GET['company'])){
        $company = $_GET['company'];
        $company_next = $_GET['company_next'];
        }
    else {
        $company = $company1;
        $company_next = $company2;
        }
    ?>

    <head>
        <title><?=$company?>: Ricerca esistenza</title>    

        <meta name="viewport" content="width=device-width, user-scalable=false;">
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <link rel="stylesheet" type="text/css" href="micronaet.css">

        <script language="JavaScript">
            //<!--
                function setFocus(){
                    document.frmCerca.codice.focus();
                    }
            // -->
        </script>
    </head>
    <body onLoad="codice.focus();">
        <table>
            <form name="frmCerca" method="get" action="status.php">
                <tr>
                    <th colspan="2" class="style1">
                        <span class="styletitle"><?=strtoupper($company)?>: RICERCA ESISTENZA PRODOTTI</span><br/>
                        <a href=<?="find.php?company=$company_next&company_next=$company"?>>PASSA A <?=strtoupper($company_next)?></a><br/>
                        <?="[ $yourbrowser ] [ $location ]"?>
                    </th>
                </tr>
                <tr>
                    <td class="style1">Codice</td>
                    <td align="center">
                        <input name="codice" type="text" id="codice" size="30">
                        <input type="hidden" name="company" value="<?=$company?>">
                        <input type="hidden" name="company_next" value="<?=$company_next?>">
                        <input type="hidden" name="browser" value="<?=$yourbrowser?>">
                    </td>
                </tr>
                <tr>
                    <td class="style1">Descrizione</td>
                    <td align="center">
                        <input name="descrizione" type="text" id="descrizione" size="30">
                    </td>
                </tr>
                <tr>
                    <td class="style1">Quantit&agrave;</td>
                    <td align="center" style="color:#fff;">
                        Da <input name="qta_da" type="number" id="qta_da" size="3" min="0" max="2000" style="width: 6em;">
                        a <input name="qta_a" type="number" id="qta_a" size="3" min="0" max="2000" style="width: 6em;">
                    </td>
                </tr>
                <tr>
                    <th colspan="2">
                        <input type="submit" name="Submit" value="Cerca">
                    </th>
                </tr>
            </form>
        </table>
        <script language="JavaScript">
            setFocus();
        </script>
    </body>
</html>
