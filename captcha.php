<?php
session_start();

$codigoCaptcha = substr(md5(time()) ,0, 8);

$_SESSION['captcha'] = $codigoCaptcha;

$imagemCaptcha = imagecreatefrompng("pngegg.png");

$fonteCaptcha = imageloadfont("anonymous.gdf");

$corCaptcha = imagecolorallocate($imagemCaptcha, 255,10,0);

imagestring($imagemCaptcha, $fonteCaptcha, 10, 70, $codigoCaptcha, $corCaptcha);

imagepng($imagemCaptcha);

imagedestroy($imagemCaptcha); 

?>