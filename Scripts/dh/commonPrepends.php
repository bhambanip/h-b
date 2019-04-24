<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/30/2018
 * Time: 1:57 PM
 */

date_default_timezone_set('America/Vancouver');

$conn = mysqli_connect("localhost", "root", "Sandpan8029", "AmzIntegrate");
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error() . ' ' . mysqli_connect_errno());
}


$excludeBrands = array('hp', 'dell', 'Elite', 'benq', 'epson', 'arozzi', 'jabra', 'Edifier', 'VI-DT', 'AXIOM', 'HEWLETT',
    'INFOCUS', 'ADD-ON', 'AXIS', 'SENNHEISER', 'YEALINK', 'HUAWEI', 'CORSAIR');

$excludeUPCs = array('889349984284', '095205761160', '17229163393', '889488474578', '766907962017', '037332157911');

$amzFirstRow = array('sku', 'product-id', 'product-id-type', 'price', 'quantity', 'add-delete',
    'will-ship-internationally', 'handling-time', 'item-condition');

define('amzDefaultMargin', '15.00');
define('amzShipping', '22');
define('amzOverSize', '95');


define('startTime', '01:00 am');
define('stopTime', '05:00 am');

$currentTime = new DateTime();
$updateStartTime = DateTime::createFromFormat('H:i a', startTime);
$updateEndTime = DateTime::createFromFormat('H:i a', stopTime);
$updateNeeded = ($currentTime > $updateStartTime) && ($currentTime < $updateEndTime);

define('minQuantity', '2');
define('minQuantityZero', '1');


// New Egg
define('newEggUrl', 'https://api.newegg.com/marketplace/can/datafeedmgmt/feeds/submitfeed?sellerid=%s&requesttype=%s');
define('newEggSellerId', 'AH6S');
define('newEggRequestType', 'ITEM_DATA');
define('newEggUpdateRequestType', 'INVENTORY_AND_PRICE_DATA');
define('newEggHeaderAuthorization', "Authorization: af6ccb717e2649e5967d0b883a40648a");
define('newEggHeaderSecretKey', "SecretKey: 33cd8b3d-a1af-45f5-9858-45374ceb42da");
define('newEggHeaderContentType', "Content-Type: application/json");
define('newEggHeaderAccept', "Accept: application/json");

define('newEggDefaultMargin', '15.00');
define('newEggShipping', '26');
define('newEggOverSize', '95');

$changeMargin = array(array('actual' => '1.00', 'real' => '1.1'),
    array('actual' => '2.00', 'real' => '2.1'),
    array('actual' => '3.00', 'real' => '3.2'),
    array('actual' => '4.00', 'real' => '4.3'),
    array('actual' => '5.00', 'real' => '5.4'),
    array('actual' => '6.00', 'real' => '6.5'),
    array('actual' => '7.00', 'real' => '7.7'),
    array('actual' => '8.00', 'real' => '8.9'),
    array('actual' => '9.00', 'real' => '10.1'),
    array('actual' => '10.00', 'real' => '11.3'),
    array('actual' => '11.00', 'real' => '12.5'),
    array('actual' => '12.00', 'real' => '13.8'),
    array('actual' => '13.00', 'real' => '15.1'),
    array('actual' => '14.00', 'real' => '16.5'),
    array('actual' => '15.00', 'real' => '17.8'),
    array('actual' => '16.00', 'real' => '19.2'),
    array('actual' => '17.00', 'real' => '20.7'),
    array('actual' => '18.00', 'real' => '22.1'),
    array('actual' => '19.00', 'real' => '23.7'),
    array('actual' => '20.00', 'real' => '25'),
);

function sendMargin($margin, $changeMargin)
{
    foreach ($changeMargin as $cMargin) {
        if (strcmp($cMargin['actual'], $margin) == 0) {
            echo " Real ".$cMargin['real']. "\n\r";
            return $cMargin['real'];
        }
    }
    return '17.8';
}

function changePrice($cost, $price)
{
    /*if(($cost >= 500) && ($cost < 800)) {
        return $price + 5;
    }
    if(($cost >= 800) && ($cost < 1000)) {
        return $price + 8;
    }
    if(($cost >= 1000) && ($cost < 1500)) {
        return $price + 15;
    }
    if(($cost >= 1500) && ($cost < 2000)) {
        return $price + 30;
    }*/
    if(($cost >= 2000) && ($cost < 2500)) {
        return $price + 45;
    }
    if(($cost >= 2500) && ($cost < 3000)) {
        return $price + 60;
    }
    if($cost >= 3000) {
        return $price + 80;
    }

    return $price;
}

$newEggManufacturer = array(array('brand' => 'VIEWSONIC - LCD', 'manufacturer' => 'ViewSonic'),
    array('brand' => 'C2G Canada', 'manufacturer' => 'Cables To Go'),
    array('brand' => 'C2G', 'manufacturer' => 'Cables To Go'),
    array('brand' => 'Cables To Go Canada', 'manufacturer' => 'Cables To Go'),
    array('brand' => 'Cisco Canada', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'Cisco Meraki', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'Cisco Refurbished', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'HP INC', 'manufacturer' => 'HP'),
    array('brand' => 'WD Bulk Canada', 'manufacturer' => 'Western Digital'),
    array('brand' => 'WD Retail Canada', 'manufacturer' => 'Western Digital'),
    array('brand' => 'HPE', 'manufacturer' => 'HP'),
    array('brand' => 'HPE-Business Class Storage', 'manufacturer' => 'HP'),
    array('brand' => 'HPE Aruba', 'manufacturer' => 'HP'),
    array('brand' => 'HPE ISS CTO', 'manufacturer' => 'HP'),
    array('brand' => 'Asus Peripherals Canada', 'manufacturer' => 'ASUS'),
    array('brand' => 'Asus Components', 'manufacturer' => 'ASUS'),
    array('brand' => 'Asus Computers', 'manufacturer' => 'ASUS'),
    array('brand' => 'Manhattan Products', 'manufacturer' => 'MANHATTAN'),
    array('brand' => 'HPE Storage BTO', 'manufacturer' => 'HP'),
    array('brand' => 'HP INC - SMARTBUY NOTEBOOK', 'manufacturer' => 'HP'),
    array('brand' => 'Logitech Canada', 'manufacturer' => 'Logitech'),
    array('brand' => 'Corsair Components', 'manufacturer' => 'Corsair'),
    array('brand' => 'Cyberpower Canada', 'manufacturer' => 'CyberPower'),
    array('brand' => 'Microsoft Hardware', 'manufacturer' => 'Microsoft'),
    array('brand' => 'MICROSOFT - SOFTWARE', 'manufacturer' => 'Microsoft'),
    array('brand' => 'Antec Canada', 'manufacturer' => 'Antec'),
    array('brand' => 'Crucial', 'manufacturer' => 'Micron Technology, Inc.'),
    array('brand' => 'D-Link Canada', 'manufacturer' => 'D-Link'),
    array('brand' => 'DLINK - BUSINESS SOLUTIONS', 'manufacturer' => 'D-Link'),
    array('brand' => 'Visiontek Canada', 'manufacturer' => 'VisionTek'),
    array('brand' => 'Aver', 'manufacturer' => 'AVerMedia'),
    array('brand' => 'HPE Networking BTO', 'manufacturer' => 'HP'),
    array('brand' => 'HPE ISS', 'manufacturer' => 'HPE'),
    array('brand' => 'LG Electronics Canada', 'manufacturer' => 'LG Electronics'),
    array('brand' => 'SteelSeries Inc.', 'manufacturer' => 'SteelSeries'),
    array('brand' => 'Plantronics CA', 'manufacturer' => 'Plantronics'),
    array('brand' => 'Avermedia Technology', 'manufacturer' => 'AVerMedia Technologies Inc.'),
    array('brand' => 'Intellinet', 'manufacturer' => 'Intellinet Network Solutions'),
    array('brand' => 'SonicWALL Canada', 'manufacturer' => 'SonicWall'),
    array('brand' => 'Sonicwall Licensing', 'manufacturer' => 'SonicWall'),
    array('brand' => 'Sandisk Canada', 'manufacturer' => 'SanDisk'),
    array('brand' => 'TI', 'manufacturer' => 'Texas Instruments'),
    array('brand' => 'Makerbot Canada', 'manufacturer' => 'MakerBot'),
    array('brand' => 'Winbo', 'manufacturer' => 'Winbo Digital'),
    array('brand' => 'Garmin Canada', 'manufacturer' => 'Garmin'),
    array('brand' => 'Zotac Canada', 'manufacturer' => 'ZOTAC'),
    array('brand' => 'STARTECH.COM - DT SB', 'manufacturer' => 'STARTECH'),
    array('brand' => 'CYBERPOWERPC', 'manufacturer' => 'CyberPower'),
    array('brand' => 'VERBATIM - AMERICAS LLC', 'manufacturer' => 'VERBATIM'),
    array('brand' => 'ELO - ACCESSORIES', 'manufacturer' => 'Elo Touch Solutions'),
    array('brand' => '3M - SUPPLIES', 'manufacturer' => '3M'),
    array('brand' => 'MODREC', 'manufacturer' => 'Modrec International Gmbh'),
    array('brand' => 'TRIPP LITE - DT', 'manufacturer' => 'Tripp Lite'),
    array('brand' => 'ADD-ON MEMORY DT', 'manufacturer' => 'AddOn'),
    array('brand' => 'SEAGATE - LACIE', 'manufacturer' => 'LaCie'),
    array('brand' => 'TRENDNET - BUSINESS', 'manufacturer' => 'TRENDnet'),
    array('brand' => 'CANON - SUPPLIES', 'manufacturer' => 'Canon'),
    array('brand' => 'VERTIV CANADA - AVOCENT', 'manufacturer' => 'Avocent'),
    array('brand' => 'ADTRAN - ACCESSORIES', 'manufacturer' => 'ADTRAN'),
    array('brand' => 'SAMSUNG - SSD', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'ACECAD - SOLIDTEK - DT', 'manufacturer' => 'SolidTek'),
    array('brand' => 'LEXMARK - CPD SUPPLIES', 'manufacturer' => 'Lexmark'),
    array('brand' => 'XEROX A4 CONSUMABLES', 'manufacturer' => 'XEROX'),
    array('brand' => 'TOSHIBA - NOTEBOOKS', 'manufacturer' => 'Toshiba'),
    array('brand' => 'INTEL - SERVER MOTHERBOARD', 'manufacturer' => 'Intel'),
    array('brand' => 'SEAGATE OEM', 'manufacturer' => 'Seagate'),
    array('brand' => 'ACER - MONITORS', 'manufacturer' => 'Acer America'),
    array('brand' => 'BROTHER - SUPPLIES', 'manufacturer' => 'Brother'),
    array('brand' => 'NEC - PROJECTOR', 'manufacturer' => 'NEC Display Solutions'),
    array('brand' => 'BENQ - ACCS & INPUT', 'manufacturer' => 'BenQ'),
    array('brand' => 'KINGSTON - DIGITAL IMAGING', 'manufacturer' => 'Kingston Technology Corp.'),
    array('brand' => 'Microsoft FPP Hardware', 'manufacturer' => 'Microsoft'),
    array('brand' => 'Lenovo Server Products', 'manufacturer' => 'Lenovo'),
    array('brand' => 'DELL - CONSUMABLES PRINTER & TONER', 'manufacturer' => 'DELL'),
    array('brand' => 'ADD-ON NETWORKING DT', 'manufacturer' => 'AddOn'),
    array('brand' => 'KINGSTON - MEMORY', 'manufacturer' => 'Kingston Technology Corp.'),
    array('brand' => 'XEROX XRC CONSUMABLES', 'manufacturer' => 'XEROX'),
    array('brand' => 'PHILIPS MONITORS', 'manufacturer' => 'Philips'),
    array('brand' => 'HP INC - SMARTBUY DESKTOP', 'manufacturer' => 'HP'),
    array('brand' => 'EPSON - PRINTERS - MULTI FUNCTION', 'manufacturer' => 'Epson America'),
    array('brand' => 'CYBER POWER SYSTEM - DT SB', 'manufacturer' => 'Cyberpower Canada'),
    array('brand' => 'INTEL - MOTHERBOARD', 'manufacturer' => 'Intel'),
    array('brand' => 'LG ELECTRONICS - DIGITAL SIGNAGE', 'manufacturer' => 'LG Electronics'),
    array('brand' => 'HPE ISS BTO HW SW', 'manufacturer' => 'HPE'),
    array('brand' => 'DELL - PERIPHERALS', 'manufacturer' => 'DELL'),
    array('brand' => 'LOGITECH - OEM', 'manufacturer' => 'Logitech'),
    array('brand' => 'HPE ARUBA SWITCHING ROUTING HW', 'manufacturer' => 'HPE'),
    array('brand' => 'WESTERN DIGITAL - DESKTOP DIRVE', 'manufacturer' => 'Western Digital'),
    array('brand' => 'SAMSUNG - SMART PHONE ACCESSORIES', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'KENSINGTON - ACCO', 'manufacturer' => 'ACCO'),
    array('brand' => 'SAMSUNG - DIGITAL SIGNAGE', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'CISCO SYSTEMS-SMALL BUSINESS 37', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'DELL - MONITORS', 'manufacturer' => 'DELL'),
    array('brand' => 'INTEL - PROCESSORS', 'manufacturer' => 'Intel'),
    array('brand' => 'CISCO SYSTEMS - ENTERPRISE', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'VERBATIM - MOBILITY', 'manufacturer' => 'VERBATIM'),
    array('brand' => 'KENSINGTON - MOBILE', 'manufacturer' => 'Kensington'),
    array('brand' => 'CANON B2B PRINTERS', 'manufacturer' => 'Canon'),
    array('brand' => 'LG ELECTRONICS - LCD', 'manufacturer' => 'LG Electronics'),
    array('brand' => 'HP INC - SMARTBUY NOTEBOOK OPTION', 'manufacturer' => 'HP'),
    array('brand' => 'POLYCOM - VOIP', 'manufacturer' => 'POLYCOM AUDIO'),
    array('brand' => 'ANS', 'manufacturer' => 'ANS INTERNATIONAL'),
    array('brand' => 'NETSCOUT HH TOOL HW-SW-SUPPORT', 'manufacturer' => 'NetScout Systems, Inc'),
    array('brand' => 'PHILIPS - BATT AND MEDIA', 'manufacturer' => 'Philips'),
    array('brand' => 'CANON LARGE FORMAT', 'manufacturer' => 'Canon'),
    array('brand' => 'SENNHEISER BUSINESS HEADSETS', 'manufacturer' => 'Sennheiser Electronic corp.'),
    array('brand' => 'INTUIT - CONSIGNMENT', 'manufacturer' => 'Intuit'),
    array('brand' => 'CLUB 3D B.V - DT', 'manufacturer' => 'Club3D'),
    array('brand' => 'HP INC - ACCESSORIES', 'manufacturer' => 'HP'),
    array('brand' => 'VIEWSONIC - DIGITAL SIGNAGE', 'manufacturer' => 'ViewSonic'),
    array('brand' => 'MICROSOFT - PC ACCESSORIES', 'manufacturer' => 'Microsoft'),
    array('brand' => 'V7 - CABLES', 'manufacturer' => 'V7'),
    array('brand' => 'BENQ - LCD', 'manufacturer' => 'BenQ'),
    array('brand' => 'HP INC - BTO NOTEBOOK OPTION', 'manufacturer' => 'HP'),
    array('brand' => 'ADTRAN - NETVANTAN INTERNET', 'manufacturer' => 'ADTRAN'),
    array('brand' => 'JEM / XTREME', 'manufacturer' => 'Xtreme-Jem Accessories'),
    array('brand' => 'KINGSTON - RETAIL DIGITAL IMAGING', 'manufacturer' => 'Kingston Technology Corp.'),
    array('brand' => 'MICROSOFT - XBOX ACCESSORIES', 'manufacturer' => 'Microsoft'),
    array('brand' => 'ATDEC - DT SB', 'manufacturer' => 'Atdec'),
    array('brand' => 'NETGEAR - CONSUMER HARDWARE', 'manufacturer' => 'Netgear Inc.'),
    array('brand' => 'HP INC - PAPER', 'manufacturer' => 'HP'),
    array('brand' => 'SAMSUNG- WIRELESS ACCESSORIES', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'CANON - NETWORKING', 'manufacturer' => 'Canon'),
    array('brand' => 'DELL - LATITUDE', 'manufacturer' => 'DELL'),
    array('brand' => 'XEROX A4 PRINTERS', 'manufacturer' => 'XEROX'),
    array('brand' => 'BENQ - AMERICA CORP', 'manufacturer' => 'BenQ'),
    array('brand' => 'NEC DISPLAY SOLUTIONS - LCD', 'manufacturer' => 'NEC Display Solutions'),
    array('brand' => 'HPE ARUBA WLAN HW SW SERVICES', 'manufacturer' => 'HP'),
    array('brand' => 'HP INC - SCITEX', 'manufacturer' => 'HP'),
    array('brand' => 'ALERATEC - DT SB', 'manufacturer' => 'Aleratec Inc.'),
    array('brand' => 'HP INC - SMARTBUY WORKSTATION', 'manufacturer' => 'HP'),
    array('brand' => 'COREL DT', 'manufacturer' => 'Corel'),
    array('brand' => 'ADD-ON COMP PERIPHERALS DT', 'manufacturer' => 'AddOn'),
    array('brand' => 'NETGEAR - SMB HARDWARE', 'manufacturer' => 'Netgear Inc.'),
    array('brand' => 'PLUSTEK TECHNOLOGY - DT SB', 'manufacturer' => 'Plustek'),
    array('brand' => 'KODAK CANADA - CONSUMABLES', 'manufacturer' => 'Kodak'),
    array('brand' => 'HP INC - BTO DESKTOP OPTION', 'manufacturer' => 'HP'),
    array('brand' => 'DA-LITE - CE', 'manufacturer' => 'DA-LITE'),
    array('brand' => 'HP INC - SMARTBUY DISPLAY', 'manufacturer' => 'HP'),
    array('brand' => 'HPE STORAGE HW SW', 'manufacturer' => 'HP'),
    array('brand' => 'INTEL - SOLID STATE DRIVES', 'manufacturer' => 'Intel'),
    array('brand' => 'MICROSOFT - ESD', 'manufacturer' => 'Microsoft'),
    array('brand' => 'HP INC - SMARTBUY WRKSTATION OPTION', 'manufacturer' => 'HP'),
    array('brand' => 'TARGUS - IPAD TABLET ACCESSORIES', 'manufacturer' => 'Targus'),
    array('brand' => 'POLYCOM - VOICE/PANO', 'manufacturer' => 'POLYCOM'),
    array('brand' => 'SAMSUNG - MEMORY', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'WESTERN DIGITAL TECH - SANDISK SSD2', 'manufacturer' => 'SanDisk'),
    array('brand' => 'EPSON - PRO GRAPHIC PRINTERS', 'manufacturer' => 'Epson America'),
    array('brand' => 'EPSON - SUPPLIES', 'manufacturer' => 'Epson America'),
    array('brand' => 'EMERGE TECH CONS', 'manufacturer' => 'Emerge Technologies, Inc.'),
    array('brand' => 'KOBO - EBOOK READERS', 'manufacturer' => 'Kobo'),
    array('brand' => 'MICROSOFT - OEM', 'manufacturer' => 'Microsoft'),
    array('brand' => 'ECO STYLE - DT', 'manufacturer' => 'Eco Style'),
    array('brand' => 'MOTOROLA MOBILITY - ACCESSORIES', 'manufacturer' => 'Motorola'),
    array('brand' => 'DLINK - CONSUMER PRODUCTS', 'manufacturer' => 'D-Link'),
    array('brand' => 'HP INC - CONSUMER', 'manufacturer' => 'HP'),
    array('brand' => 'CISCO SYSTEMS - COBO', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'KENSINGTON - ACCO ACCESSORIES', 'manufacturer' => 'Kensington'),
    array('brand' => 'XEROX A3 CONSUMABLES', 'manufacturer' => 'XEROX'),
    array('brand' => 'VIEWSONIC - PROJECTORS', 'manufacturer' => 'ViewSonic'),
    array('brand' => 'KODAK DOCUMENT IMAGING-ALARIS', 'manufacturer' => 'Eastman Kodak Co.'),
    array('brand' => 'SYMANTEC - RETAIL BOX', 'manufacturer' => 'Symantec'),
    array('brand' => 'HPE ARUBA PROMO OFFICE CONNECT I5', 'manufacturer' => 'HP'),
    array('brand' => 'AMERICAN BATTERY COMPANY - DT', 'manufacturer' => 'American Battery'),
    array('brand' => 'LOGITECH - HARMONY CE', 'manufacturer' => 'Logitech'),
    array('brand' => 'KOBO - ACCESSORIES', 'manufacturer' => 'Kobo, Inc'),
    array('brand' => 'QUANTUM - MEDIA', 'manufacturer' => 'Quantum Corporation'),
    array('brand' => 'HPE MEDIA STORAGE', 'manufacturer' => 'HP'),
    array('brand' => 'INFOCUS - PROJECTORS', 'manufacturer' => 'InFocus'),
    array('brand' => 'ELO - TOUCH SCREENS', 'manufacturer' => 'Elo Touch Solutions'),
    array('brand' => 'BIC INC', 'manufacturer' => 'BIC'),
    array('brand' => 'V7 - KEYBOARDS AND MICE', 'manufacturer' => 'V7'),
    array('brand' => 'LEXMARK - PARTS', 'manufacturer' => 'Lexmark'),
    array('brand' => 'NETGEAR - ARLO', 'manufacturer' => 'Netgear Inc.'),
    array('brand' => 'SHARP DIGITAL SIGNAGE', 'manufacturer' => 'Sharp'),
    array('brand' => 'DELL CANADA - PRINTERS AND SUPPLIES', 'manufacturer' => 'DELL'),
    array('brand' => 'TARGUS - ACCESSORIES', 'manufacturer' => 'Targus'),
    array('brand' => 'HP INC - SMARTBUY MOBILE WRKSTATION', 'manufacturer' => 'HP Commercial Specialty'),
    array('brand' => 'LEXMARK - BSDP SUPPLIES', 'manufacturer' => 'Lexmark'),
    array('brand' => 'ROGERS WIRELESS', 'manufacturer' => 'Rogers Communications, Inc'),
    array('brand' => 'DELL - DESKTOPS', 'manufacturer' => 'DELL'),
    array('brand' => 'V7 - CASES', 'manufacturer' => 'V7'),
    array('brand' => 'NEC - DIGITAL SIGNAGE', 'manufacturer' => 'NEC Display Solutions'),
    array('brand' => 'HP INC - MANAGED PRINT', 'manufacturer' => 'HP'),
    array('brand' => 'HP INC - OFFICEJET PRO X', 'manufacturer' => 'HP'),
    array('brand' => 'VIEWSONIC - VA SERIES', 'manufacturer' => 'ViewSonic'),
    array('brand' => 'EVOLIS - DT', 'manufacturer' => 'Evolis, Inc'),
    array('brand' => 'MCKLEIN COMPANY DT', 'manufacturer' => 'McKlein USA'),
    array('brand' => 'ACER - ACCESSORIES', 'manufacturer' => 'Acer America'),
    array('brand' => 'BLACKBERRY ACCS', 'manufacturer' => 'BLACKBERRY'),
    array('brand' => 'NUANCE - DRAGON MEDICAL BOX', 'manufacturer' => 'NUANCE'),
    array('brand' => 'DANBY PRODUCTS LIMITED', 'manufacturer' => 'Danby'),
    array('brand' => 'SAMSUNG - LCD', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'HPE DCN HW SW', 'manufacturer' => 'Hpe - Top Of Rack'),
    array('brand' => 'CISCO - TEMP PROMO 2', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'FUJITSU - CONSUMABLES', 'manufacturer' => 'Fujitsu'),
    array('brand' => 'HPE - ARUBA NETWORKING', 'manufacturer' => 'HP'),
    array('brand' => 'JABRA - GN US', 'manufacturer' => 'Jabra Enterprise Products'),
    array('brand' => 'ADTRAN - BLUESOCKET', 'manufacturer' => 'ADTRAN'),
    array('brand' => 'DELL - ENTERPRISE', 'manufacturer' => 'DELL'),
    array('brand' => 'FUJI PHOTO FILM - DATA MEDIA', 'manufacturer' => 'FUJIFILM'),
    array('brand' => 'HP INC - SUPPLIES KITS', 'manufacturer' => 'HP'),
    array('brand' => 'HP INC - BTO WORKSTATION OPTION', 'manufacturer' => 'HP'),
    array('brand' => 'EPSON - PAPER', 'manufacturer' => 'Epson America'),
    array('brand' => 'DELL - WYSE', 'manufacturer' => 'DELL'),
    array('brand' => 'WATER-STREAM', 'manufacturer' => 'GENERIC'),
    array('brand' => 'GOLLA OY - STAPLES CONSIGNMENT DT', 'manufacturer' => 'Golla'),
    array('brand' => 'SAMSUNG - IT CONSUMABLES', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'AROZZI - DT', 'manufacturer' => 'Arozzi'),
    array('brand' => 'MOTOROLA MOBILITY UNLOCKED HANDSETS', 'manufacturer' => 'Motorola'),
    array('brand' => 'POLYCOM - VIDEO', 'manufacturer' => 'POLYCOM'),
    array('brand' => 'AVG - DT', 'manufacturer' => 'AVG'),
    array('brand' => 'JASCO CONSIGNED', 'manufacturer' => 'Jasco Products'),
    array('brand' => 'SAMSUNG -B2C DISPLY', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'LENOVO DCG OPTIONS', 'manufacturer' => 'Lenovo'),
    array('brand' => 'EPSON - DOCUMENT SCANNER', 'manufacturer' => 'Epson America'),
    array('brand' => 'HP INC - BTO DISPLAY', 'manufacturer' => 'HP'),
    array('brand' => 'HONEYWELL CONS.', 'manufacturer' => 'Honeywell'),
    array('brand' => 'V7 - GLOBAL DISPLAYS', 'manufacturer' => 'V7'),
    array('brand' => 'LENOVO CANADA-OPTIONS', 'manufacturer' => 'Lenovo'),
    array('brand' => 'LENOVO CANADA - TOPSELLER DT', 'manufacturer' => 'Lenovo'),
    array('brand' => 'LENOVO CANADA - TOPSELLER TP', 'manufacturer' => 'Lenovo'),
    array('brand' => 'CYPRESS SOLUTIONS', 'manufacturer' => 'CYPRESS'),
    array('brand' => 'IK MULTIMEDIA - DT', 'manufacturer' => 'IK Multimedia'),
    array('brand' => 'COREL - WORDPERFECT', 'manufacturer' => 'Corel'),
    array('brand' => 'CISCO SYSTEMS - IP TELEPHONY', 'manufacturer' => 'Cisco Systems, Inc.'),
    array('brand' => 'LENOVO CANADA - DISPLAYS', 'manufacturer' => 'Lenovo'),
    array('brand' => 'WESTERN DIGITAL - RETAIL DRIVES', 'manufacturer' => 'Western Publishing Company, Inc.'),
    array('brand' => 'LENOVO STORAGE AND NETWORKING', 'manufacturer' => 'Lenovo'),
    array('brand' => 'SOROC TECHNOLOGY', 'manufacturer' => 'HP'),
    array('brand' => 'INTUIT CANADA', 'manufacturer' => 'Intuit'),
    array('brand' => 'MICROSOFT - XBOX', 'manufacturer' => 'Microsoft'),
    array('brand' => 'EPSON - CONSIGMENT', 'manufacturer' => 'Epson America'),
    array('brand' => 'HP INC - SCANNERS', 'manufacturer' => 'HP'),
    array('brand' => 'GOOD NATURED DT', 'manufacturer' => 'Good Natured Products'),
    array('brand' => 'LENOVO DCG TS SERVER CHASSIS', 'manufacturer' => 'Lenovo'),
    array('brand' => 'INTEL - SERVER PROCESSORS', 'manufacturer' => 'Intel'),
    array('brand' => 'SANDISK RETAIL 1', 'manufacturer' => 'SanDisk'),
    array('brand' => 'HP INC - PRESARIO', 'manufacturer' => 'HP'),
    array('brand' => 'SONICWALL - NFR AND HA PRODUCTS', 'manufacturer' => 'SonicWall'),
    array('brand' => 'CANON - E COMMERCE', 'manufacturer' => 'Canon'),
    array('brand' => 'HP INC - POS', 'manufacturer' => 'HP'),
    array('brand' => 'POLYCOM - MS VOICE', 'manufacturer' => 'POLYCOM'),
    array('brand' => 'HP INC - S PRINT LONG LIFE CONSUM', 'manufacturer' => 'HP'),
    array('brand' => 'PEERLESS - AV', 'manufacturer' => 'Peerless'),
    array('brand' => 'IRIS - DT', 'manufacturer' => 'IRIS'),
    array('brand' => 'WESTERN DIGITAL - SSD', 'manufacturer' => 'Western Digital'),
    array('brand' => 'ALURATEK - CONSIGNMENT', 'manufacturer' => 'Aluratek'),
    array('brand' => 'WASP - BARCODE', 'manufacturer' => 'Wasp Barcode'),
    array('brand' => 'NETSCOUT AIRMAGNET MOBILE', 'manufacturer' => 'NetScout Systems, Inc'),
    array('brand' => 'TELUS DEVICES', 'manufacturer' => 'Telus'),
    array('brand' => 'QUANTUM - P SERIES', 'manufacturer' => 'Quantum'),
    array('brand' => 'HP INC - INK', 'manufacturer' => 'HP'),
    array('brand' => 'SANDISK RETAIL 3', 'manufacturer' => 'SanDisk'),
    array('brand' => 'SAMSUNG - UNLOCK DEVICES', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'HP INC - BTO NOTEBOOK', 'manufacturer' => 'HP'),
    array('brand' => 'FELLOWES - MOBILITY', 'manufacturer' => 'FELLOWES'),
    array('brand' => 'FUJI PHOTO FILM - CAMERAS', 'manufacturer' => 'FUJIFILM'),
    array('brand' => 'HP INC - TONER', 'manufacturer' => 'HP'),
    array('brand' => 'FUJITSU - NOTEBOOKS', 'manufacturer' => 'Fujitsu'),
    array('brand' => 'BARSKA - DT', 'manufacturer' => 'BARSKA'),
    array('brand' => 'HPE IOT', 'manufacturer' => 'HP'),
    array('brand' => 'SAMSUNG - HOSPITALITY TVS', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'KOSS - STAPLESCONS', 'manufacturer' => 'Koss'),
    array('brand' => 'MCAFEE - RETAIL', 'manufacturer' => 'McAfee'),
    array('brand' => 'CON-PYRAMID TIME SYSTEMS', 'manufacturer' => 'Pyramid Time Systems'),
    array('brand' => 'TCL ACCESSORIES', 'manufacturer' => 'TCL Communications'),
    array('brand' => 'MICROSOFT - SURFACE ACCESSORIES', 'manufacturer' => 'Microsoft'),
    array('brand' => 'DELL - BMO CTO', 'manufacturer' => 'DELL'),
    array('brand' => 'CLARITY PRODUCTS', 'manufacturer' => 'Clarity'),
    array('brand' => 'SUMMITSOFT - DT', 'manufacturer' => 'Summitsoft'),
    array('brand' => 'VIEWSONIC - CE', 'manufacturer' => 'ViewSonic'),
    array('brand' => 'HP INC - SMARTBUY THIN CLIENT', 'manufacturer' => 'HP'),
    array('brand' => 'GARTNER STUDIO', 'manufacturer' => 'Gartner Studios'),
    array('brand' => 'XEROX - VIS. SCANNERS', 'manufacturer' => 'XEROX'),
    array('brand' => 'SONICWALL - NSA HARDWARE', 'manufacturer' => 'SonicWall'),
    array('brand' => 'FUJITSU - MOBILITY ACCESSORIES', 'manufacturer' => 'Fujitsu'),
    array('brand' => 'PLANTRONICS - CLARITY', 'manufacturer' => 'Clarity'),
    array('brand' => 'LEXMARK - CONFIG', 'manufacturer' => 'Lexmark'),
    array('brand' => 'JUNIPER - EX SERIES', 'manufacturer' => 'Juniper Networks, Inc.'),
    array('brand' => 'SAMSUNG - TABLETS', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'SAMSUNG - B2B UNLOCK DEVICES', 'manufacturer' => 'SAMSUNG'),
    array('brand' => 'PLENOM - DT', 'manufacturer' => 'Plenom'),
    array('brand' => 'JUNIPER - ENTERPRISE FIREWALL', 'manufacturer' => 'Juniper Networks, Inc.'),
    array('brand' => 'ADTRAN- PHONES', 'manufacturer' => 'ADTRAN'),
    array('brand' => 'ARCCOS GOLF - DT', 'manufacturer' => 'Arccos'),
    array('brand' => 'HP INC - BTO THIN CLIENT', 'manufacturer' => 'HP'),
    array('brand' => 'ADD-ON COMPUTER PERIPHERALS', 'manufacturer' => 'AddOn'),
    array('brand' => 'C2G (CABLES TO GO)', 'manufacturer' => 'Cables To Go'),
    array('brand' => 'DYMO/CARDSCAN/ROLODEX', 'manufacturer' => 'DYMO'),
    array('brand' => 'LENOVO COMMERCIAL', 'manufacturer' => 'Lenovo'),
    array('brand' => 'LENOVO GLOBAL TECHNOLOGY', 'manufacturer' => 'Lenovo'),
    array('brand' => 'KONICA COPIER/FAX SUPPLIES', 'manufacturer' => 'Konica Minolta'),
    array('brand' => 'D-LINK SOLUTIONS', 'manufacturer' => 'D-Link'),
    array('brand' => 'OKI PRINTING SOLUTIONS', 'manufacturer' => 'Oki Data'),
    array('brand' => 'MICROSOFT OEM SW', 'manufacturer' => 'Microsoft'),
    array('brand' => 'MICROSOFT HW', 'manufacturer' => 'Microsoft'),
    array('brand' => 'MICROSOFT CONSUMER', 'manufacturer' => 'Microsoft'),
    array('brand' => 'KYOCERA DOCUMENT SOLUTIONS', 'manufacturer' => 'Kyocera'),
    array('brand' => 'PHILIPS SPEECH PRODUCTS', 'manufacturer' => 'Philips'),
    array('brand' => 'DREMEL 3D', 'manufacturer' => 'DREMEL'),
    array('brand' => 'CITIZEN SYSTEM', 'manufacturer' => 'Citizen'),
    array('brand' => 'RIVA CASE', 'manufacturer' => 'RIVACASE'),
    array('brand' => 'SIMPLIFIED IT', 'manufacturer' => 'Simplified IT Products, LLC'),
    array('brand' => 'CLOVER IMAGING GROUP', 'manufacturer' => 'DATAPRODUCTS'),
);
?>
