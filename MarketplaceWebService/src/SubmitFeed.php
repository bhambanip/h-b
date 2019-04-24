#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/19/2018
 * Time: 8:49 PM
 */
include_once('.config.inc.php');

$service = new MarketplaceWebService_Client(
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY,
    $CONFIG,
    APPLICATION_NAME,
    APPLICATION_VERSION
);

$hour = date('h');
$fileNo = intval($hour) % 2;
$feedHandle = @fopen($UPLOAD_FILES[$fileNo], 'rw+');
rewind($feedHandle);
$parameters = array (
    'Merchant' => MERCHANT_ID,
    'MarketplaceIdList' => $MARKETPLACE_ID_ARRAY,
    'FeedType' => '_POST_FLAT_FILE_INVLOADER_DATA_',
    'FeedContent' => $feedHandle,
    'PurgeAndReplace' => false,
    'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    // 'ContentType'=> 'text/tab-separated-values; charset=iso-8859-1',
//  'MWSAuthToken' => '<MWS Auth Token>', // Optional
);

rewind($feedHandle);

postAmazonFeed($service, $parameters);

@fclose($feedHandle);
function postAmazonFeed($service, $parameters)
{
    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);

    try {
        $response = $service->submitFeed($request);

        echo ("Service Response\n");
        echo ("=============================================================================\n");

        echo("        SubmitFeedResponse\n");
        if ($response->isSetSubmitFeedResult()) {
            echo("            SubmitFeedResult\n");
            $submitFeedResult = $response->getSubmitFeedResult();
            if ($submitFeedResult->isSetFeedSubmissionInfo()) {
                echo("                FeedSubmissionInfo\n");
                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                if ($feedSubmissionInfo->isSetFeedSubmissionId())
                {
                    echo("                    FeedSubmissionId\n");
                    echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                }
                if ($feedSubmissionInfo->isSetFeedType())
                {
                    echo("                    FeedType\n");
                    echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                }
                if ($feedSubmissionInfo->isSetSubmittedDate())
                {
                    echo("                    SubmittedDate\n");
                    echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                }
                if ($feedSubmissionInfo->isSetFeedProcessingStatus())
                {
                    echo("                    FeedProcessingStatus\n");
                    echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                }
                if ($feedSubmissionInfo->isSetStartedProcessingDate())
                {
                    echo("                    StartedProcessingDate\n");
                    echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                }
                if ($feedSubmissionInfo->isSetCompletedProcessingDate())
                {
                    echo("                    CompletedProcessingDate\n");
                    echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                }
            }
        }
        if ($response->isSetResponseMetadata()) {
            echo("            ResponseMetadata\n");
            $responseMetadata = $response->getResponseMetadata();
            if ($responseMetadata->isSetRequestId())
            {
                echo("                RequestId\n");
                echo("                    " . $responseMetadata->getRequestId() . "\n");
            }
        }

        echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
    } catch (MarketplaceWebService_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    }
}