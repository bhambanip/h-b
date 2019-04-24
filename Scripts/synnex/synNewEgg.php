<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 7/4/2018
 * Time: 5:58 PM
 */
include_once('../dh/commonPrepends.php');
include_once('../dh/newEggItem.php');

$url = sprintf(newEggUrl, newEggSellerId, newEggRequestType);
$content = generateData($conn, $newEggManufacturer);
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

function generateData($conn, $newEggManufacturer)
{
    $query = "SELECT * FROM synItemList WHERE Brand not in ('AXIOM MEMORY', 'ADD-ON COMPUTER PERIPHERALS')";
    $result = mysqli_query($conn, $query) or die('Errant query:  ' . $query);
    /* create one master array of the records */
    $items = array();
    if (mysqli_num_rows($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['Quantity']) {
                $basicInfo = new stdClass();
                $basicInfo->Item = new stdClass();
                $basicInfo->Item->BasicInfo = new newEggItem($row, $newEggManufacturer);
                $items[] = $basicInfo;
            }
        }
    }

    $itemFeed = array('Itemfeed' => $items);

    $newEggEnvelope = new stdClass();
    $newEggEnvelope->Header = new stdClass();
    $newEggEnvelope->Header->DocumentVersion = "2.0";
    $newEggEnvelope->MessageType = "BatchItemCreation";
    $newEggEnvelope->Message = $itemFeed;

    return json_encode(array('NeweggEnvelope' => $newEggEnvelope));
}

?>