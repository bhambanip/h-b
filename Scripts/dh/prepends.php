<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/8/2018
 * Time: 7:42 PM
 */
define('importFileExt', '.xls');

// D&H Credentials
define('dhFtp', 'ftp.dandh.com');
define('dhUser', '8113870000');
define('dhPassword', 'Fey2g3zX8fyK');
// D&H Server files

define('dhPrefix', 'dh');
$dhServerFiles = array('ITEMLIST', 'CATLIST');
define('dhServerFileDelimiter', '|');

define('dhColumnQuantity', '1');
define('dhColumnSku', '4');
define('dhColumnUpc', '6');
define('dhColumnSubCategoryNo', '7');
define('dhColumnBrand', '8');
define('dhColumnPrice', '9');
define('dhColumnShortDesc', '15');
define('dhColumnLongDesc', '16');
define('dhColumnWeight', '14');

// Amz File
define('dhAmzFile', '/var/www/html/MarketplaceWebService/data/dhAmzFile.txt');

$dhExcludeSkus = array('8332211CA', 'GCDVIIFFCA', 'CL15UTL', 'ZA2T0001USCA', 'USBBT1EDR4CA',
    'B2B064C00CA', 'ZA2T0001USCA', 'ZA2T0001USCA', 'ZBOXCI549NANOUC', '08GP46183KR', 'ZBOXCI327NAUWCA',
    'ZBOXMI549NAUCA', '900918CA', '900908CA', 'S041568CA', 'ZBOXMI552UCA', '19381CA', '821660311110CA',
    '821660111017CA', '821660111055CA', '821660111086CA', '821660111307CA', '821660111512CA', '821660112403CA',
    '821660112410CA', '821660130100CA', '821660130162CA', '821660130476CA', '821660131213CA', '821660131497CA',
    '821660131503CA', '821660131527CA', '821660131855CA', '821660131862CA', '821660131916CA', '821660111185CA',
    '821660111000CA', '821660112267CA', '821660110782CA', '821660131848CA', '821660131879CA', '821660131923CA',
    'SV411KUSBCN', 'SDSSDA480GG26CA', 'SDSSDA240GG26CA', '8332211CA', '81AX00BWUSCA', 'WD101KFBXCA');
$dhExcludeSkusRegulation = array('20730901CA');
?>