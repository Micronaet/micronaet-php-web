<?php
    require_once('./Mysql.class.php');
    $mysql = new Mysql('localhost', 'micronaet', 'fiam_pm_manager', 'Y3urHjF9bfd96tPp');
    
    if(!$mysql->connect()){
        var_dump('Micronaet: Impossibile collegarsi al DB - '.$mysql->error->getMessage());
        die;
        }

    $mysql->selectDb('micronaet');

    // Read company data:
    $company = isset($_GET['company']) ? $_GET['company']: "fia";
    $company_next = isset($_GET['company_next']) ? $_GET['company_next']: "gpb";

    //Read filter data:
    $codice = isset($_GET['codice']) ? $_GET['codice'] : null;
    $descrizione = isset($_GET['descrizione']) ? $_GET['descrizione'] : null;
    $qta_da = isset($_GET['qta_da']) && trim($_GET['qta_da']) != '' ? (int)$_GET['qta_da'] : null;
    $qta_a = isset($_GET['qta_a']) && trim($_GET['qta_a']) != '' ? (int)$_GET['qta_a'] : null;

    // Query generation: default
    $q = "select m.* from magazzino_$company m where 1=1 ";
    
    // Query generation: code filter:
    if(!is_null($codice) && trim($codice) != ''){
        $q .= " and trim(upper(codice)) like trim(upper('%$codice%'))";
        }

    // Query generation: description filter:
    if(!is_null($descrizione) && trim($descrizione) != ''){
        $key = explode(' ', $descrizione);
        $q .= " and  ( 1=1 ";
        $filtroCounter = 0;
        foreach ($key as $v) {
            $q .= " and
            trim(upper(c.descrizione)) like trim(upper('%$v%'))
            ";
            }
        $q .= " ) ";
        }

    // Query generation: from / to qta:
    if(!is_null($qta_da) && trim($qta_da) != ''){
        $q .= " and dispo_lorda >= $qta_da ";
        }

    if(!is_null($qta_a) && trim($qta_a) != ''){
        $q .= " and dispo_lorda <= $qta_a ";
        }
        
    $q .= ";";
    $esito = $mysql->query($q);
    ?>
    <html>
      <head>
        <title><?=strtoupper($company)?> Stato materiali</title>
        <style type="text/css">
    <!--
    <?php
      $browser = strtoupper($_GET["browser"]);
      if ($browser == "BROWSER"){
          $h_font="font-size: 13px;";
          $b_table_w="800px";
          }
      elseif ($browser == "IPHONE"){
          $h_font="font-size: 24px;";
          $b_table_w="100%";
          }
      elseif ($browser == "BB"){
          $h_font="font-size: 9px;";
          $b_table_w="100%";
          }
      else {
          $h_font="font-size: 12px;";
          $b_table_w="100%";
          }
    ?>

    body,td,th {
        <?php echo $h_font; ?>
        color: #333333;
        font-family: Verdana, Arial, Helvetica, sans-serif;
        }
    body {
        background-color: #FFFFFF; 
        margin: 0px;
        }
    .style9 {
        color: #FFFFFF; 
        font-weight: bold;
        }
    .style10 {color: #FFFFFF; }
    .styleRed {color: #FF0000; }
    .styleGreen {color: #008800;}
    a:link {color: #FFFFFF;}
    a:visited {color: #FFFFFF;}
    a:hover {color: #FFFF33; }
    a:active {color: #FFFFFF; }
    .style13 {
        <?php echo $h_font; ?>
        color: #FFFFFF;
        font-weight: bold;
        }
    .tr_stileP {background-color: #FFFFFF;}
    .tr_stileD {background-color: #FFFFCC;}
    table *{
        color:#000;
        font-weight:bold;
        }
    td {padding:2px; }
    -->
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

    <body>
    <table width="<?php echo $b_table_w; ?>" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
     <tr align="center" bgcolor="#666666">
     <td height="20"><span class="style9"><a href='<?php echo "find.php?company=$company&company_next=$company_next";?>'><?=strtoupper($company)?> Ricerca:</a></span></td>
     <td height="30" colspan="8" align="left"><span class="style9">Cod.:<?php echo strtoupper($_GET["codice"]); ?><br>
      Desc.:<?=strtoupper($_GET["descrizione"])?></span><span class="style10"></span><span class="style10"></span></td>
     </tr>
     <tr align="center" bgcolor="#003366">
      <td height="20"><span class="style13">Immagine</span></td>
      <td height="20"><span class="style13">Codice</span></td>
      <td height="20"><span class="style13">Descrizione</span></td>
      <td height="20"><span class="style13">Disponibili</span></td>
      <!--<td height="20"><span class="style13">Ordine fornitore</span></td>
      <td height="20"><span class="style13">Arrivo fornitore</span></td>-->
      <td height="20"><span class="style13">Impegnati clienti</span></td>
      <!--<td height="20"><span class="style13">Impegnati campagne</span>-->
      <td height="20"><span class="style13">Prezzo</span></td>
      <td height="20"><span class="style13">Status</span></td>
     </tr>
    <?php
    $count = 0;
    foreach($esito as $x){
        $bgcolor = $count % 2 == 0 ? '#eae9AA' : '#f0f0f0';

        //if($x['esistenza'] <= 0){
        //    $bgcolor = '#f19393';
        //    }
        $codice = $x['codice'];
        $descrizione = $x['descrizione'];
        $esistenza = $x['esistenza'];
        //$sospesi_cliente = $x['sospesi_cliente'];
        $ordinati = $x['ordinati'];
        $dispo_lorda = $x['dispo_lorda'];
        $prezzo = number_format($x['prezzo'], 2, ',','');
        $status = $x['status'];
        /*
        $quantity = $x['esistenza'];
        $campagne = $x['campagne'];
        $ordinati = $x['ordinati'];
        $data_arrivo = $x['data_arrivo'];
        $prenotato = $x['sospesi_cliente'];

        $disponibile = (float)$quantity - (float)$prenotato - (float)$campagne;
        $bgcolor2 = '#f19393';
         if((float)$disponibile <= 0){
            $bgcolor2 = '#f19393';
            }

        if((float)$disponibile > 0){
            $bgcolor2 = '#feed01';
            }

        if((float)$disponibile >= 10){
            $bgcolor2 = '#2ed245';
            }

        $image = '';
        $image800 = '';
        $image1200 = '';
        $image2000 = '';
         if($x['image'] != ''){
             $image = "http://www.gpb.it/includes/displayThumb.php?image=/var/www/html/pm.fiam.it/site/public/http/www/webdata/image_upload/".$x['image']."&w=100&h=100";
             $image = '<img src="'.$image.'" alt="" />';

             $image800 = "http://www.gpb.it/includes/displayThumb.php?image=/var/www/html/pm.fiam.it/site/public/http/www/webdata/image_upload/".$x['image']."&w=800&h=800";
             $image1200 = "http://www.gpb.it/includes/displayThumb.php?image=/var/www/html/pm.fiam.it/site/public/http/www/webdata/image_upload/".$x['image']."&w=1200&h=1200";
             $image2000 = "http://www.gpb.it/includes/displayThumb.php?image=/var/www/html/pm.fiam.it/site/public/http/www/webdata/image_upload/".$x['image']."&w=2000&h=2000";
        } */

        echo "<tr style='background:$bgcolor;'>";
        /*if($image != ''){
            echo "<td>
	        $image
	        <p>
	            <a style='color:#555;' href='$image800' target='_blank'>800x800</a> |
		    <a style='color:#555;' href='$image1200' target='_blank'>1200x1200</a> |
		    <a style='color:#555;' href='$image2000' target='_blank'>2000x2000</a>
                </p>
	     </td>";}
            else {
                echo "<td>$image</td>";
	        }*/

        echo "<td style='background:$bgcolor;'>&nbsp;</td>";
        echo "<td style='background:$bgcolor;'>$codice</td>";
        echo "<td style='background:$bgcolor;'>$descrizione</td>";
        echo "<td style='background:$bgcolor2;'>$esistenza</td>";
        echo "<td style='background:$bgcolor;'>&nbsp</td>";
        echo "<td style='background:$bgcolor;'>$prezzo</td>";
        //echo "<td>&nbsp;</td>";
        //echo "<td>&nbsp;</td>";
        //echo "<td>&nbsp;</td>";
        echo "<td style='background:$bgcolor;'>$status</td>";
        echo "</tr>";
        $count++;
    }
?>
