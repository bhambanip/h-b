<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 9/6/2018
 * Time: 8:26 AM
 */
require_once('.config.inc.test.php');
$service = new MarketplaceWebServiceProducts_Client(
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY,
    APPLICATION_NAME,
    APPLICATION_VERSION,
    $CONFIG);

$request = new MarketplaceWebServiceProducts_Model_GetMyPriceForSKURequest();
$request->setSellerId(MERCHANT_ID);
$request->setMarketplaceId(MARKETPLACE);


$sku_list = new MarketplaceWebServiceProducts_Model_SellerSKUListType();
$sku_list->setSellerSKU('128112');
$request->setSellerSKUList($sku_list);
$sku = '128112';


/*$result = mysqli_query($conn, "SELECT * FROM " . $argv[1] . " where (Quantity != 0) and (Status != 0)");
while ($res = mysqli_fetch_array($result)) {
    $request->setSellerSKU($res['Sku']);
    $lowestPrice = getLowerPrice($res['Sku'], $request, $service);
    if ($lowestPrice == "InvalidParameterValue") {
        $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET AmzPrice=0, AmzStatus=0 WHERE Id=" . $res['Id'] . "");
    } else {
        $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET AmzPrice='$lowestPrice' WHERE Id=" . $res['Id'] . "");
    }
}*/
try {
    $response = $service->GetMyPriceForSKU($request);
    if ($response->isSetGetMyPriceForSKUResult()) {
        $myPrice = $response->getGetMyPriceForSKUResult()[0];
        if ($myPrice->isSetSellerSKU() && $myPrice->isSetProduct() && ($myPrice->getSellerSKU() == $sku)) {
            $product = $myPrice->getProduct();
            if ($product->isSetOffers()) {
                $offers = $product->getOffers();
                echo $offers->isSetOffer();
            }
        }
    }
    /*echo $myPrice;
    echo ("Service Response\n");
    echo ("=============================================================================\n");

    $dom = new DOMDocument();
    $dom->loadXML($response->toXML());
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    echo $dom->saveXML();
    echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");*/

} catch (MarketplaceWebServiceProducts_Exception $ex) {
    echo("Caught Exception: " . $ex->getMessage() . "\n");
    echo("Response Status Code: " . $ex->getStatusCode() . "\n");
    echo("Error Code: " . $ex->getErrorCode() . "\n");
    echo("Error Type: " . $ex->getErrorType() . "\n");
    echo("Request ID: " . $ex->getRequestId() . "\n");
    echo("XML: " . $ex->getXML() . "\n");
    echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
}

