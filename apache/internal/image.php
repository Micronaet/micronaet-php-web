<?php
    // open the file in a binary mode:
    $code = $_GET['code'];
    $name = "./images/$code.jpg";
    die($name);
    $fp = fopen($name, 'rb');
    //if (file_exists($name)) {
        // send the right headers
        header("Content-Type: image/jpeg");
        header("Content-Length: " . filesize($name));
        fpassthru($name);
        ////readfile($name);
        //imagejpeg($name); // dump the picture and stop the script
        //imagedestroy($name); // Free up memory
        exit;
    //    }        
?>
