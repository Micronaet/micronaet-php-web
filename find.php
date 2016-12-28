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
    $company1 = 'FIA';
    $company2 = 'GPB';
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
            body, td, th {
                font-size: xx-small;
                color: #333333;
                font-family: Verdana, Arial, Helvetica, sans-serif;
                }
            tr {
                background-color: #003366;
                }    
            th, th>td {
                background-color: #003300;
                text-align: center;
                } 
            table {
                width: 350px;
                border: 1px;
                border-color: #333333;
                /*cellpadding: 0px;
                cellspacing: 0px; */
                }   
            body {
                background-color: #FFFFFF;
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
                }
            input {
                font-family: Verdana, Arial, Helvetica, sans-serif;
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
                <th align="center">
                    <td height="40" colspan="3">
                        <span class="styletitle"><?=$company?>: RICERCA ESISTENZA PRODOTTI</span><br/>
                        <span class="style1">
                            <a href=<?="find.php?company=$company_next&company_next=$company"?>><?="SELEZIONA $company_next"?></a>
                        </span><br/>
                        <!--(agg.:<?php echo " " . $d; ?> - tot. <?php echo " " . $tot; ?>)-->
                        <span class="style1">[ <?=$yourbrowser?> ]</span>
                  </td>
                </th>
                <tr>
                    <td height="40"><span class="style1">&nbsp;Codice</span></td>
                    <td height="40" colspan="2" align="center"><input name="codice" type="text" id="codice"></td>
                    <input type="hidden" name="company" value="<?=$company?>">
                    <input type="hidden" name="company_next" value="<?=$company_next?>">
                </tr>
                <tr>
                    <td height="40"><span class="style1">&nbsp;Descrizione</span></td>
                    <td height="40" colspan="2" align="center"><input name="descrizione" type="text" id="descrizione"></td>
                </tr>
                <tr>
                    <td height="40"><span class="style1">&nbsp;Quantit&agrave;</span></td>
                    <td height="40" colspan="2" align="center" style="color:#fff;">
                      Da <input name="qta_da" type="number" id="qta_da" size="5"><br/> a <input name="qta_a" type="number" id="qta_a" size="5">
                    </td>
                </tr>
                <th>
                    <td height="35" colspan="3"><input type="submit" name="Submit" value="Cerca">
                       <input type="hidden" name="browser" value="<?=$yourbrowser?>"></td>
                </th>
            </form>
        </table>
        <script language="JavaScript">
            setFocus();
        </script>
    </body>
</html>
