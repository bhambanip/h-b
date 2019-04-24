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

    $request = new MarketplaceWebServiceProducts_Model_GetMyFeesEstimateRequest();
    $request->setSellerId(MERCHANT_ID);

    $result = mysqli_query($conn, "SELECT * FROM " . $argv[1] . " where (Quantity != 0) and (Status != 0)  and (Price != 0) and (AmzStatus != 0) order by AmzFees");
    while ($res = mysqli_fetch_array($result)) {
        $fessEstimate = new MarketplaceWebServiceProducts_Model_FeesEstimateRequestList();
        $fessEstimateRequest = new MarketplaceWebServiceProducts_Model_FeesEstimateRequest();
        $fessEstimateRequest->setMarketplaceId(MARKETPLACE);
        $fessEstimateRequest->setIdType('SellerSKU');
        $fessEstimateRequest->setIdValue($res['Sku']);
        $fessEstimateRequest->setIdentifier($res['Sku']);

        $listPrice = ($res['Price'] * 1.15) + 22;
        $priceToEstimate = new MarketplaceWebServiceProducts_Model_PriceToEstimateFees();
        $listingPrice = new MarketplaceWebServiceProducts_Model_MoneyType();
        $listingPrice->setAmount($listPrice);
        $listingPrice->setCurrencyCode('CAD');
        $priceToEstimate->setListingPrice($listingPrice);
        $fessEstimateRequest->setPriceToEstimateFees($priceToEstimate);

        $fessEstimate->setFeesEstimateRequest($fessEstimateRequest);

        $request->setFeesEstimateRequestList($fessEstimate);

        $amzFees = getMyFees($res['Sku'], $listPrice, $request, $service);
        $updateResult = mysqli_query($conn, "UPDATE " . $argv[1] . " SET AmzFees='$amzFees' WHERE Id=" . $res['Id'] . "");
    }
}

function getMyFees($sku, $listPrice, $request, MarketplaceWebServiceProducts_Interface $service)
{
    try {
        $response = $service->GetMyFeesEstimate($request);
        if ($response->isSetGetMyFeesEstimateResult()) {
            $myFeesResult = $response->getGetMyFeesEstimateResult();
            if ($myFeesResult->isSetFeesEstimateResultList()) {
                $myFeesResultList = $myFeesResult->getFeesEstimateResultList();
                if ($myFeesResultList->isSetFeesEstimateResult()) {
                    $feesEstimateResult = $myFeesResultList->getFeesEstimateResult()[0];
                    if ($feesEstimateResult->isSetStatus() && ($feesEstimateResult->getStatus() == "Success")) {
                        $feesEstimateIdentifier = $feesEstimateResult->getFeesEstimateIdentifier();
                        if ($feesEstimateIdentifier->isSetIdValue() && ($feesEstimateIdentifier->getIdValue() == $sku)) {
                            $feesEstimate = $feesEstimateResult->getFeesEstimate()->getTotalFeesEstimate()->getAmount();
                            return round(($feesEstimate / $listPrice) * 100);
                        }
                    }
                }
            }
        }
        return 0;
    } catch (MarketplaceWebServiceProducts_Exception $ex) {
        echo("XML: " . $ex->getXML() . "\n");
        return 0;
    }
}