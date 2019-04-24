<?php
date_default_timezone_set('America/Vancouver');
$UPLOAD_FILES = array('/var/www/html/MarketplaceWebService/data/dhAmzFile.txt', '/var/www/html/MarketplaceWebService/data/ingramAmzFile.txt');
$hour = date('h');
echo $hour . " " . "Pankaj ";
$fileNo = intval($hour) % 2;
echo $fileNo." ";
echo $UPLOAD_FILES[$fileNo]." ";

phpinfo();
?>