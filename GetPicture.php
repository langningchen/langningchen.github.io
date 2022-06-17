<?php
    session_start();
    $image = imagecreatetruecolor(100, 30);
    $bgcolor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $bgcolor);
    $captch_code="";
    for ($i = 0; $i < 4 ; $i++) { 
        $fontsize = 6;
        $fontcolor = imagecolorallocate($image,rand(0,120),rand(0,120),rand(0,120));
        $data="abcdefghijkmnpqrstuvwxyz23456789";
        $fontcontent = substr($data, rand(0, strlen($data) - 1), 1);
        $captch_code .= $fontcontent;
        $x = ($i * 100 / 4) + rand(5, 15);
        $y = rand(0, 15);

        imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
    }
    $_SESSION["AuthCode"] = $captch_code;
    for ($i = 0; $i < 100; $i++) { 
        imagesetpixel($image, rand(1, 99), rand(1, 29), imagecolorallocate($image,rand(50, 200), rand(50, 200), rand(50, 200)));
    }
    for ($i = 0; $i < 3; $i++) { 
        imageline($image, rand(1, 99), rand(1, 29),rand(1, 99), rand(1, 29), imagecolorallocate($image,rand(50, 200), rand(50, 200), rand(50, 200)));
    }
    header("content-type:image/png");
    imagepng($image);
?>
