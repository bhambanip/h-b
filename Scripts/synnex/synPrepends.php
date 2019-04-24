<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/8/2018
 * Time: 7:42 PM
 */
// Synnex Credentials
define('synFtp', 'ftp.synnex.ca');
define('synUser', 'c1211377');
define('synPassword', '9k5rN2W3');

// Synnex Server files
define('synPrefix', 'syn');
define('synZipFile', 'c1211377.zip');
define('synFile', '1211377.ap');
define('synLocalFile', 'SYN.TXT');

//Synnex database
define('synItemList', 'synItemList');

//Synnex Columns
define('synAvailabilityFlag', '5');
define('synColumnQuantity', '9');
define('synColumnManufacturerQuantity', '69');
define('synColumnSku', '4');
define('synColumnUpc', '33');
define('synColumnSubCategoryNo', '24');
define('synColumnBrand', '7');
define('synColumnPrice', '12');
define('synColumnShortDesc', '6');
define('synColumnLongDesc', '6');
define('synColumnWeight', '27');
define('synColumnManufacturerSku', '2');
define('synColumnLength', '52');
define('synColumnWidth', '53');
define('synColumnHeight', '54');

// Amz File
define('synAmzFile', '/var/www/html/MarketplaceWebService/data/synAmzFile.txt');

$synExcludeSkus = array('syn6177544', 'syn5708119', 'syn5663969', 'syn6044806',
    'syn6177901', 'syn5988500', 'syn6081201', 'syn6078610', 'syn6073219', 'syn5720138', 'syn5525912', 'syn5135195', 'syn6044806',
    'syn5589485', 'syn4449654', 'syn5756994', 'syn4572608', 'syn5495435', 'syn4145115', 'syn5495434', 'syn5353291',
    'syn5663969', 'syn4861742', 'syn4783281', 'syn4628850', 'syn4755359', 'syn4585098', 'syn9002123', 'syn4465374',
    'syn5960925', 'syn5967612', 'syn5295444', 'syn4602448', 'syn4907489', 'syn4907468', 'syn5721857', 'syn5216005',
    'syn4863841', 'syn6038380', 'syn6088757', 'syn4657738', 'syn5708082', 'syn4181823', 'syn4237395', 'syn5776403',
    'syn6257927', 'syn6187882', 'syn6229298', 'syn4840574');
$synExcludeSkusRegulation = array('syn5805237', 'syn4237395', 'syn5776403', 'syn6242307');
?>