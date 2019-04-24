#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 9/4/2018
 * Time: 11:09 PM
 */
require_once('.config.inc.test.php');

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

$request->setSellerSKU('ZBOXEN51050UWC');
$lowestPrice = getLowerPrice('ZBOXEN51050UWC', $request, $service);
echo $lowestPrice;


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
                        }
                    }
                }
            }
        }
        return 0;
    } catch (MarketplaceWebServiceProducts_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        return 0;
    }
}