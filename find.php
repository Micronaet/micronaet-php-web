<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <?php
    if(!session_id()) session_start();

    //verifico se la richiesta è nella lan locale
    $ips = array('91.187.199.104','91.187.199.105','91.187.199.106','91.187.199.107');

    if(!isset($_SESSION['logged'])){
        if(!in_array($_SERVER['REMOTE_ADDR'],$ips)) {
            header('Location: loginmic.php');
            die;
            }
        else {
            $_SESSION['logged'] = true;
            }
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
    <meta name="viewport" content="width=device-width, user-scalable=false;">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title><?=$company?>: Ricerca esistenza</title>
    
    <script language="JavaScript">
        <!--
            function setFocus(){
                document.frmCerca.codice.focus();
                }
        // -->
        </script>
            <style type="text/css">
            <!--
            body {
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: xx-small;
                color: #333333;
                background-color: #FFFFFF;
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
                }
            table {
                width: 350px;
                border: 0px;
                /*border-color: #333333;*/
                padding: 0px;
                border-spacing: 0px;
                }
            th {
                background-color: #002200;
                text-align: center;
                } 
            td {
                background-color: #003366;
                height: 40px;
                }    
            input {
                font-size: x-small;
                }
            .style1 {
                color: #FFFFFF;
                font-weight: bold;
                }
            .styletitle {
                color: yellow;
                font-weight: bold;
                }
                
            /* unvisited link */
            a:link {
                color: #00FF00;
                }

            /* visited link */
            a:visited {
                color: #00FF00;
                }

            /* mouse over link */
            a:hover {
                color: red;
                }

            /* selected link */
            a:active {
                color: yellow;
                }                 
            -->
        </style>
    </head>
    <body onLoad="codice.focus();">
        <table>
            <form name="frmCerca" method="get" action="status.php">
                <tr>
                    <th colspan="2" class="style1">
                        <span class="styletitle"><?=strtoupper($company)?>: RICERCA ESISTENZA PRODOTTI</span><br/>
                        <a href=<?="find.php?company=$company_next&company_next=$company"?>>PASSA A <?=strtoupper($company_next)?></a><br/>
                        [ <?=$yourbrowser?> ]
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
                        Da <input name="qta_da" type="number" id="qta_da" size="3" min="0" max="1000" style="width: 6em;">
                        a <input name="qta_a" type="number" id="qta_a" size="3" min="0" max="1000" style="width: 6em;">
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
