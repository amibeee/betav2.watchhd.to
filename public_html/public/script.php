<?php
$mysecretkey = "mysecretkey";
$path = "live/testerhd23";
$addr = $_SERVER['REMOTE_ADDR'];
$expiry = strtotime("+1 hour");
$b64 = base64_encode(md5($mysecretkey.$path.$addr.$expiry,true));
$b64u = rtrim(str_replace(array('+','/'),array('-','_'),$b64),'=');
echo "rtmp://81.169.242.127:1935/$path?e=$expiry&st=$b64u\n";
?>