#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 9/4/2018
 * Time: 11:09 PM
 */
require_once('.config.inc.php');
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

$query = mysqli_query($conn, "SELECT * FROM synItemList where (Quantity != 0) and (Status != 0)  and (AmzStatus != 0) Order by AmzLowestPriceUpdate LIMIT 120");

while ($res = mysqli_fetch_array($query)) {
    $amzLowestPrice = 0;
    $request->setSellerSKU($res['Sku']);
    $lowestPrice = getLowerPrice($res['Sku'], $request, $service);
    if ($lowestPrice != -100) {
        $updateResult = mysqli_query($conn, "UPDATE synItemList SET AmzMinPrice='$lowestPrice', 
            AmzLowestPrice='$amzLowestPrice', AmzLowestPriceUpdate = now() WHERE Id=" . $res['Id'] . "");
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