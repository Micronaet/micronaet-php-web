<?php
    // open the file in a binary mode:
    $name = './images/Varie Roberta/FOTO OK/SCONTORNATE.THUMB/541675.png';
    $fp = fopen($name, 'rb');
    die($name);
    if (file_exists($name)) {
        // send the right headers
        header("Content-Type: image/jpg");
        header("Content-Length: " . filesize($name));

        // dump the picture and stop the script
        fpassthru($fp);
        exit;
        }        
?>
errore
