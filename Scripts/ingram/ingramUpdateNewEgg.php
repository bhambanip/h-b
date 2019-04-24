<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 7/4/2018
 * Time: 5:58 PM
 */
include_once('/var/www/html/Scripts/dh/commonPrepends.php');
include_once('/var/www/html/Scripts/dh/newEggUpdateItem.php');

$url = sprintf(newEggUrl, newEggSellerId, newEggUpdateRequestType);
$content = generateUpdateData($conn, $changeMargin);
$header = array(newEggHeaderAuthorization, newEggHeaderSecretKey, newEggHeaderContentType, newEggHeaderAccept);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

echo json_decode($json_response, true);

function generateUpdateData($conn, $changeMargin)
{
    $query = "SELECT * FROM ingramItemList";
    $result = mysqli_query($conn, $query) or die('Errant query:  ' . $query);
    /* create one master array of the records */
    $items = array();
    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $price = $row['Price'] + newEggShipping;
            $price = changePrice($row['Price'], $price);
            $priceQuery = mysqli_query($conn, "SELECT * FROM ingramCatList where SubCategoryNo ='" . $row['SubCategoryNo'] . "'");
            if (mysqli_num_rows($priceQuery) > 0) {
                foreach ($priceQuery as $priceResult) {
                    $margin = $priceResult['MarginNewEgg'] != 0 ? $priceResult['MarginNewEgg'] : newEggDefaultMargin;
                }
            } else {
                $margin = newEggDefaultMargin;
            }

            if (($row['Height'] > 30.00) || ($row['Length'] > 30.00)) {
                $price += newEggOverSize;
            }

            $margin = sendMargin((string)$margin, $changeMargin);
            $price = $price * (1 + $margin / 100);

            $items[] = new newEggUpdateItem($row, $price);
        }
    }

    $itemFeed = array('Item' => $items);

    $newEggEnvelope = new stdClass();
    $newEggEnvelope->Header = new stdClass();
    $newEggEnvelope->Header->DocumentVersion = "1.0";
    $newEggEnvelope->MessageType = "Inventory";
    $newEggEnvelope->Overwrite = "No";
    $newEggEnvelope->Message = new stdClass();
    $newEggEnvelope->Message->Inventory = $itemFeed;

    return json_encode(array('NeweggEnvelope' => $newEggEnvelope));
}

?>