<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/8/2018
 * Time: 7:42 PM
 */
define('importFileExt', '.xls');
define('asiExportFileExt', '.csv');

// D&H Credentials
define('asiFtp', '67.104.19.215');
define('asiUser', '106105');
define('asiPassword', 'Sandpan8029');
// D&H Server files

define('asiPrefix', 'asi');
define('asiItemFile', asiUser . asiExportFileExt);

define('asiColumnQuantityVancouver', '6');
define('asiColumnQuantityToronto', '5');
define('asiColumnSku', '0');
define('asiColumnUpc', '13');
define('asiColumnSubCategoryNo', '11');
define('asiColumnCategoryNo', '4');
define('asiColumnBrand', '3');
define('asiColumnPrice', '7');
define('asiColumnShortDesc', '1');
define('asiColumnManufacturerSku', '1');
define('asiColumnLongDesc', '2');
define('asiColumnWeight', '8');
define('asiColumnStatus', '12');

// Amz File
define('asiAmzFile', '/var/www/html/MarketplaceWebService/data/asiAmzFile.txt');

$asiExcludeSkus = array('187974', '189261', '128304', '220804', '222178');
$asiExcludeSkusRegulation = array('130295');
?>