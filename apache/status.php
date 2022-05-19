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
    $is_root_text = 'AGENTE';
    if ($_GET['root'] == true){
        $is_root_text = 'ADMIN';
        }
    //Read filter data:
    $codice = isset($_GET['codice']) ? $_GET['codice'] : null;
    $descrizione = isset($_GET['descrizione']) ? $_GET['descrizione'] : null;
    $qta_da = isset($_GET['qta_da']) && trim($_GET['qta_da']) != '' ? (int)$_GET['qta_da'] : null;
    $qta_a = isset($_GET['qta_a']) && trim($_GET['qta_a']) != '' ? (int)$_GET['qta_a'] : null;
    $shop = isset($_GET['shop']) ? $_GET['shop'] : null;

    // Query generation: default
    $q = "select m.* from magazzino_$company m where 1=1 ";

    // Filter only product if company 1: code is 2:
    if ($_GET['root'] != true and $_GET['company'] == "fia"){
        $q .= " and inventory = 2";
        }

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
            trim(upper(m.descrizione)) like trim(upper('%$v%'))
            ";
            }
        $q .= " ) ";
        }

    // Query generation: from / to qta:
    if(!is_null($qta_da) && trim($qta_da) != ''){
        $q .= " and esistenza >= $qta_da ";
        }

    if(!is_null($qta_a) && trim($qta_a) != ''){
        $q .= " and esistenza <= $qta_a ";
        }

    // Query generation: end the query:
    $q .= ";";

    // Query generation: run!
    $esito = $mysql->query($q);

    // Read datetime last update table:
    //$q_time = "SELECT UPDATE_TIME ut FROM information_schema.tables WHERE TABLE_SCHEMA = 'micronaet' AND TABLE_NAME = 'magazzino_$company';";
    //$sql_update_data = $mysql->query($q_time);
    $last_udate = '';
    //foreach($sql_update_data as $x){
    //    $last_udate = $x['ut'];
    //    }

    // CSS style media dependent:
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
<html>
    <head>
        <title><?=strtoupper($company)?> Stato materiali [<?=$last_update?>]</title>
        <style type="text/css">
            <!--
            body,td,th {
                <?=$h_font?>
                color: #333333;
                font-family: Verdana, Arial, Helvetica, sans-serif;
                }
            td {
                height: 20px;
                }
            body {
                background-color: #FFFFFF;
                margin: 0px;
                }
            .style9 {
                color: #FFFFFF;
                font-weight: bold;
                }
            .style10 {
                color: #FFFFFF;
                }
            .styleRed {
                color: #FF0000;
                }
            .styleGreen {
                color: #008800;
                }
            a:link {
                color: #FFFFFF;
                }
            a:visited {
                color: #FFFFFF;
                }
            a:hover {
                color: #FFFF33;
                }
            a:active {
                color: #FFFFFF;
                }
            .style13 {
                <?php echo $h_font; ?>
                color: #FFFFFF;
                font-weight: bold;
                }
            .number {
                <?php echo $h_font; ?>
                text-align: right;
                }
            .tr_stileP {
                background-color: #FFFFFF;
                }
            .tr_stileD {
                background-color: #FFFFCC;
                }
            table *{
                color: #000;
                font-weight: bold;
                }
            td {
                padding: 2px;
                }
            -->
        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <body>
        <table width="<?php echo $b_table_w; ?>" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
            <tr align="center" bgcolor="#666666">
                <td class="style9">
                    <a href='<?php echo "find.php?shop=$shop&company=$company&company_next=$company_next";?>'><?=strtoupper($company)?> Ricerca:</a>
                </td>
                <td colspan="1" align="left" class="style9">
                    <?=$is_root_text?>
                </td>
                <td colspan="14" align="left" class="style9">
                    Cod.: <?=strtoupper($_GET["codice"]);?> Desc.: <?=strtoupper($_GET["descrizione"])?>
                </td>
            </tr>
            <tr align="center" bgcolor="#003366">
                <td class="style13">Immagine</td>
                <td class="style13">Codice</td>
                <td class="style13">Descrizione</td>

                <td class="style13">Disponibili</td>
                <?php if ($_GET['root'] == true){ ?>
                    <td class="style13">Dispo netta</td>
                <?php } ?>
                <td class="style13">Ordini fornitori</td>
                <td class="style13">Date arrivo</td>
                <?php if ($_GET['root'] == true){ ?>
                    <td class="style13">Ordini clienti</td>
                <?php } ?>
                <td class="style13">Campagne</td>

            <?php if ($_GET['root'] == true){ ?>
                <?php if ($shop != "1"){ ?>
                    <td class="style13">Costo (um forn.)</td>
                    <td class="style13">Costo F/magazzino</td>
                    <td class="style13">Costo F/Cliente</td>
                    <td class="style13">Dazi</td>
                    <td class="style13">Container</td>
                    <td class="style13">Listino 50+20</td>
                <?php } ?>
            <?php } ?>
                <td class="style13">Prezzo di listino</td>
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
                $dispo_lorda = $x['esistenza'] - $x['sospesi_cliente'];
                //$x['dispo_lorda'];
                $sospesi_cliente = $x['sospesi_cliente'];
                $campagna = $x['campagna'];
                $ordinati = $x['ordinati'];
                $data_arrivo = $x['data_arrivo'];

                $costo = number_format($x['costo'], 2, ',','');
                $costo2 = number_format($x['costo2'], 2, ',','');
                $costo1 = number_format($x['costo1'], 2, ',','');
                $dazi = number_format($x['dazi'], 2, ',','');
                $container = $x['container'];

                $prezzo5020 = number_format($x['prezzo'] * 0.5 * 0.8, 2, ',','');
                $prezzo = number_format($x['prezzo'], 2, ',','');

                $bgcolor2 = '#99FF99';

                // Dispo Lorda for GPB:
                if ($company == 'gpb'){
                    $dispo_lorda += $ordinati;
                }

                if ((float)$dispo_lorda <= 0){
                    $bgcolor2 = '#FF9999';
                    }
                if ((float)$ordinati > 0 and $company == 'gpb'){
                    $bgcolor2 = '#fcf47e';
                }

                // $status = $x['status'];

                /*
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
                if ((float)$ordinati > 0 and $company == 'gpb'){
                    $bgcolor2 = '#fcf47e';
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

                    $codice_immagine=str_replace(' ', '_', $codice);
                    echo "<td style='background:$bgcolor;'><img src='image.php?code=$codice_immagine&company=$company' width='64'/></td>";

                    echo "<td style='background:$bgcolor;'>$codice</td>";
                    echo "<td style='background:$bgcolor;'>$descrizione</td>";

                    echo "<td style='background:$bgcolor2;' class='number'>$dispo_lorda</td>";
                    if ($_GET['root'] == true){
                        echo "<td style='background:$bgcolor;' class='number'>$esistenza</td>";
                        }
                    echo "<td style='background:$bgcolor;' class='number'>$ordinati</td>";
                    echo "<td style='background:$bgcolor;'>$data_arrivo&nbsp;</td>";
                    if ($_GET['root'] == true){
                        echo "<td style='background:$bgcolor;' class='number'>$sospesi_cliente</td>";
                        }
                    echo "<td style='background:$bgcolor;' class='number'>$campagna</td>";

                    if ($_GET['root'] == true){
                        if ($shop != "1"){
                            echo "<td style='background:$bgcolor;' class='number'>$costo</td>";
                            echo "<td style='background:$bgcolor;' class='number'>$costo2&euro;</td>";
                            echo "<td style='background:$bgcolor;' class='number'>$costo1&euro;</td>";
                            echo "<td style='background:$bgcolor;'>$dazi</td>";
                            echo "<td style='background:$bgcolor;'>$container</td>";
                            echo "<td style='background:$bgcolor;' class='number'>$prezzo5020&euro;</td>";
                        }}

                        echo "<td style='background:$bgcolor;' class='number'>$prezzo&euro;</td>";
                echo "</tr>";
                $count++;
                }
            ?>
