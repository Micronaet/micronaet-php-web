<?php
    // open the file in a binary mode:
    $code = $_SESSION['code'];
    $name = "./images/$code.jpg";
    $fp = fopen($name, 'rb');
    //if (file_exists($name)) {
        // send the right headers
        header("Content-Type: image/jpg");
        header("Content-Length: " . filesize($name));

        // dump the picture and stop the script
        fpassthru($fp);
        exit;
    //    }        
?>
