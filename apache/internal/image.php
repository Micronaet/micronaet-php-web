<?php
    // todo remove put in root folder and change path and web server
    // open the file in a binary mode:
    $code = $_GET['code'];
    $company = $_GET['company'];

    //$name = "./immagini/$code.jpg";
    $name = "./images_$company/$code.jpg";

    $fp = fopen($name, 'rb');

    if (file_exists($name)) {
        // Mode 1:
        header("Content-Type: image/jpg");
        header("Content-Length: " . filesize($name));
        fpassthru($fp);
        //readfile($name);

        //Mode 3:
        //imagejpeg($name); // dump the picture and stop the script
        //imagedestroy($name); // Free up memory
        exit;
        }
     else {
        die("Image not found: $name");
        //return no image logo!
        }
?>
