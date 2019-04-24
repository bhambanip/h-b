<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/8/2018
 * Time: 7:42 PM
 */
define('importFileExt', '.xls');

// D&H Credentials
define('ingramFtp', 'partnerreports.ingrammicro.com');
define('ingramUser', 'LM23E7');
define('ingramPassword', 'Fty214');
// D&H Server files

define('ingramPrefix', 'ingram');
define('ingramPriceZipFile', 'PRICE.ZIP');
define('ingramPriceFile', 'PRICE.TXT');
define('ingramPriceDirectory', '\FUSION\CA\YS52V9\\');

define('ingramQuantityFile', 'TOTAL.TXT');
define('ingramQuantityDirectory', '\FUSION\CA\AVAIL\\');

define('ingramCatFile', 'NEWCATS.TXT');
define('ingramCatDirectory', '\FUSION\US\NEWCATS\\');

define('ingramLocalQuantityFile', 'QTY.TXT');

define('ingramServerFileDelimiter', '\t');

define('ingramAvailabilityFlag', '16');
define('ingramColumnQuantity', '1');
define('ingramColumnSku', '1');
define('ingramColumnUpc', '9');
define('ingramColumnSubCategoryNo', '20');
define('ingramColumnBrand', '3');
define('ingramColumnPrice', '14');
define('ingramColumnShortDesc', '4');
define('ingramColumnLongDesc', '5');
define('ingramColumnWeight', '8');

// Amz File
define('ingramAmzFile', '/var/www/html/MarketplaceWebService/data/ingramAmzFile.txt');

$ingramExcludeSkus = array('86529U', '7310CM', '71745J', '66053F', '40170M', '35427R', '6024ZJ', '1214DA', '4831XD', '6592ZF', '5215CZ', '5834ZF');
$ingramExcludeSkusRegulation = array('B01MTQTWB7', 'B01MDTZDQB', 'B01N55MQCU', 'B01N97VCBN',
    'B01NCAL1LV', 'B01N671NHE', 'B01MYQ01K6', '2559CG', '6447DD', '1992CL', '91919K', '21089Q', '5528DV',
    '6443DD', '4937CZ', '6444DD', '6445DD', '6442DD', '6441DD', '6446DD', '31770T', '21089Q');
?>