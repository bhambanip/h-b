#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 9/4/2018
 * Time: 11:09 PM
 */
require_once('.config.inc.php');

foreach ($itemTables as $table) {
    $query = mysqli_query($conn, "SELECT * FROM amzMinPriceTable WHERE TableName='" . $table . "'");
    if (!mysqli_num_rows($query)) {
        $insertQuery = mysqli_query($conn, "INSERT INTO amzMinPriceTable (TableName) VALUES ('" . $table . "')");
    }
}

$updateRecords = mysqli_query($conn, "SELECT * FROM amzMinPriceTable");
while ($updateRecordsRes = mysqli_fetch_array($updateRecords)) {
    $countRows = mysqli_query($conn, "SELECT COUNT(*) TotalRows FROM " . $updateRecordsRes['TableName'] . " where (Quantity != 0) and (Status != 0) and (AmzStatus != 0)");
    while ($rowsResult = mysqli_fetch_array($countRows)) {
        $updateRows = mysqli_query($conn, "UPDATE amzMinPriceTable SET TotalRecords = " . $rowsResult['TotalRows'] . " WHERE Id = " . $updateRecordsRes['Id'] . "");
    }
}


$selectTable = mysqli_query($conn, "SELECT * FROM amzMinPriceTable where Updating = 1 LIMIT 1");
if (!mysqli_num_rows($selectTable)) {
    $selectTable = mysqli_query($conn, "SELECT * FROM amzMinPriceTable order by Id LIMIT 1");
}
while ($selectTableRes = mysqli_fetch_array($selectTable)) {
    $lowerLimit = $selectTableRes['CountAt'];
    $upperLimit = 100;
    $updatedRecords = 0;
    if ($lowerLimit < 0) {
        $lowerLimit = 0;
    }

    $service = new MarketplaceWebServiceProducts_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $CONFIG);

    $request = new MarketplaceWebServiceProducts_Model_GetLowestPricedOffersForSKURequest();
    $request->setSellerId(MERCHANT_ID);
    $request->setMarketplaceId(MARKETPLACE);
    $request->setItemCondition('New');

    $result = mysqli_query($conn, "SELECT * FROM " . $selectTableRes['TableName'] . " where (Quantity != 0) and (Status != 0)  and (AmzStatus != 0)  Order by Id 
    LIMIT " . $lowerLimit . ", " . $upperLimit . "");
    while ($res = mysqli_fetch_array($result)) {
        $amzLowestPrice = 0;
        $request->setSellerSKU($res['Sku']);
        $lowestPrice = getLowerPrice($res['Sku'], $request, $service);
        if ($lowestPrice != -100) {
            $updateResult = mysqli_query($conn, "UPDATE " . $selectTableRes['TableName'] . " SET AmzMinPrice='$lowestPrice', 
            AmzLowestPrice='$amzLowestPrice' WHERE Id=" . $res['Id'] . "");
            $updatedRecords++;
        }
    }

    $totalRecordUpdated = $lowerLimit + $upperLimit - 2;
    if ($totalRecordUpdated < $selectTableRes['TotalRecords']) {
        $countAtRecord = $lowerLimit + $updatedRecords - 2;
        $updateCount = mysqli_query($conn, "UPDATE amzMinPriceTable SET CountAt='$countAtRecord', Updating=1 WHERE Id=" . $selectTableRes['Id'] . "");
    } else {
        $updatePreviousRecord = mysqli_query($conn, "UPDATE amzMinPriceTable SET CountAt=0, Updating=0");
        $nextRecord = mysqli_query($conn, "SELECT * FROM amzMinPriceTable WHERE Id > " . $selectTableRes['Id'] . " ORDER BY Id LIMIT 1");
        while ($nextRecordResult = mysqli_fetch_array($nextRecord)) {
            $updateNextRecord = mysqli_query($conn, "UPDATE amzMinPriceTable SET CountAt=0, Updating=1 WHERE Id=" . $nextRecordResult['Id'] . "");
        }
    }
}


function getLowerPrice($sku, $request, MarketplaceWebServiceProducts_Interface $service)
{
    try {
        $response = $service->GetLowestPricedOffersForSKU($request);
        if ($response->isSetGetLowestPricedOffersForSKUResult()) {
            $lowestPriceOffers = $response->getGetLowestPricedOffersForSKUResult();
            if ($lowestPriceOffers->isSetOffers() && $lowestPriceOffers->isSetSKU() && ($lowestPriceOffers->getSKU() == $sku)) {
                $offers = $lowestPriceOffers->getOffers();
                if ($offers->isSetOffer()) {
                    foreach ($offers->getOffer() as $offer) {
                        if ($offer->getMyOffer() == "false") {
                            return $offer->getListingPrice()->getAmount() + $offer->getShipping()->getAmount();
                        } else {
                            $GLOBALS['amzLowestPrice'] = 1;
                        }
                    }
                }
            }
        }
        return 0;
    } catch (MarketplaceWebServiceProducts_Exception $ex) {
        echo("XML: " . $ex->getXML() . "\n");
        return -100;
    }
}