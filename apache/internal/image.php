<?php
    // open the file in a binary mode:
    $code = $_SESSION['code'];
    $name = "./images/$code.jpg";
    $fp = fopen($name, 'rb');
    //if (file_exists($name)) {
        // send the right headers
        header("Content-Type: image/jpeg");
        //header("Content-Length: " . filesize($name));
        //fpassthru($fp);
        ////readfile($name);
        imagejpeg($name); // dump the picture and stop the script
        imagedestroy($name); // Free up memory
        exit;
    //    }        
?>
