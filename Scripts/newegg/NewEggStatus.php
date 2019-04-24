#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 9/8/2018
 * Time: 1:17 PM
 */

include_once('../dh/commonPrepends.php');
$url = sprintf('https://api.newegg.com/marketplace/can/contentmgmt/item/price?sellerid=%s', newEggSellerId);

$header = array(newEggHeaderAuthorization, newEggHeaderSecretKey, newEggHeaderContentType, newEggHeaderAccept);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_POST, true);

if ($argv[1]) {
    $result = mysqli_query($conn, "SELECT * FROM " . $argv[1] . " where (Quantity != 0) and (Status != 0)  and (Price != 0)");
    while ($res = mysqli_fetch_array($result)) {
        $content = generateData($res['Sku']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $jsonResult = curl_exec($curl);
        $jsonResult = formatJson($jsonResult);
        $neObj = json_decode($jsonResult, true);
        if (array_key_exists('SellerPartNumber', $neObj) && ($neObj['SellerPartNumber'] == $res['Sku'])) {
            $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET NESku='" . $neObj['ItemNumber'] . "', 
            NEStatus=" . $neObj['Active'] . " WHERE Id=" . $res['Id'] . "");
        } else {
            $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET NEStatus=0 WHERE Id=" . $res['Id'] . "");
        }
    }
    curl_close($curl);
}


function formatJson($jsonResult)
{
    for ($i = 0; $i <= 31; ++$i) {
        $jsonResult = str_replace(chr($i), "", $jsonResult);
    }
    $jsonResult = str_replace(chr(127), "", $jsonResult);

    if (0 === strpos(bin2hex($jsonResult), 'efbbbf')) {
        $jsonResult = substr($jsonResult, 3);
    }
    return $jsonResult;
}

/*$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


var_dump($obj);*/


function generateData($sku)
{
    $item = new stdClass();
    $item->Type = "1";
    $item->Value = $sku;
    return json_encode($item);
}