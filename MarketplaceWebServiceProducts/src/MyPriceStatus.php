#!/usr/bin/php -q
<?php
if ($argv[1]) {
    /**
     * Created by IntelliJ IDEA.
     * User: programmer
     * Date: 9/6/2018
     * Time: 8:26 AM
     */
    require_once('.config.inc.php');
    $service = new MarketplaceWebServiceProducts_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $CONFIG);

    $request = new MarketplaceWebServiceProducts_Model_GetMyPriceForSKURequest();
    $request->setSellerId(MERCHANT_ID);
    $request->setMarketplaceId(MARKETPLACE);

    $result = mysqli_query($conn, "SELECT * FROM " . $argv[1] . " where (Quantity != 0) and (Status != 0)  and (Price != 0) order by AmzStatus");
    while ($res = mysqli_fetch_array($result)) {
        $sku_list = new MarketplaceWebServiceProducts_Model_SellerSKUListType();
        $sku_list->setSellerSKU($res['Sku']);
        $request->setSellerSKUList($sku_list);
        $lowestPrice = getMyPrice($res['Sku'], $request, $service);
        if($lowestPrice) {
            $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET AmzStatus=1 WHERE Id=" . $res['Id'] . "");
        } else {
            $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET AmzStatus=0 WHERE Id=" . $res['Id'] . "");
        }
    }
}

function getMyPrice($sku, $request, MarketplaceWebServiceProducts_Interface $service)
{
    try {
        $response = $service->GetMyPriceForSKU($request);
        if ($response->isSetGetMyPriceForSKUResult()) {
            $myPrice = $response->getGetMyPriceForSKUResult()[0];
            if ($myPrice->isSetSellerSKU() && $myPrice->isSetProduct() && ($myPrice->getSellerSKU() == $sku)) {
                $product = $myPrice->getProduct();
                if ($product->isSetOffers()) {
                    $offers = $product->getOffers();
                    if ($offers->isSetOffer()) {
                        return true;
                    }
                }
            }
        }
        return false;
    } catch (MarketplaceWebServiceProducts_Exception $ex) {
        echo("XML: " . $ex->getXML() . "\n");
    }
}