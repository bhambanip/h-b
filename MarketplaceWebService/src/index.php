<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/10/2018
 * Time: 2:30 PM
 */
include_once('.config.inc.php');

$service = new MarketplaceWebService_Client(
    AWS_ACCESS_KEY_ID,
    AWS_SECRET_ACCESS_KEY,
    CONFIG,
    APPLICATION_NAME,
    APPLICATION_VERSION
);

$date1 = new DateTime();;
$list = getAmazonFeedStatus($service);
?>
    <!DOCTYPE html>
    <html>
    <head>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
<body>
    <h2>Amazon Feed Results</h2>
<table class='tutorial-table'>
    <thead>
    <tr>
        <th>Feed ID:</th>
        <th>Type:</th>
        <th>Date Sent:</th>
        <th>Status:</th>
        <th>StartedProcessingDate:</th>
        <th>CompletedProcessingDate:</th>
    </tr>
    </thead>
    <tbody>
<?php
if ($list) {
    foreach ($list as $feed) {
        ?>
        <tr>
            <?php if ($feed->isSetFeedSubmissionId()) { ?>
                <td><?php echo $feed->getFeedSubmissionId() . $date1->format('Y-m-d H:i:sP') ?></td>
            <?php } else { ?>
                <td</td>
            <?php } ?>

            <?php if ($feed->isSetFeedType()) { ?>
                <td><?php echo $feed->getFeedType() ?></td>
            <?php } else { ?>
                <td></td>
            <?php } ?>

            <?php if ($feed->isSetSubmittedDate()) { ?>
                <td><?php echo $feed->getSubmittedDate()->setTimezone(new DateTimeZone('PST'))->format(DATE_FORMAT) ?></td>
            <?php } else { ?>
                <td></td>
            <?php } ?>

            <?php if ($feed->isSetFeedProcessingStatus()) { ?>
                <td><?php echo $feed->getFeedProcessingStatus() ?></td>
            <?php } else { ?>
                <td></td>
            <?php } ?>

            <?php if ($feed->isSetStartedProcessingDate()) { ?>
                <td><?php echo $feed->getStartedProcessingDate()->setTimezone(new DateTimeZone('PST'))->format(DATE_FORMAT) ?></td>
            <?php } else { ?>
                <td></td>
            <?php } ?>

            <?php if ($feed->isSetCompletedProcessingDate()) { ?>
                <td><?php echo $feed->getCompletedProcessingDate()->setTimezone(new DateTimeZone('PST'))->format(DATE_FORMAT) ?></td>
            <?php } else { ?>
                <td></td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
    </table>
    </body>
<?php }

function getAmazonFeedStatus($service)
{
    $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest();
    $request->setMerchant(MERCHANT_ID);

    $response = $service->getFeedSubmissionList($request);

    if ($response->isSetGetFeedSubmissionListResult()) {
        $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
        return $getFeedSubmissionListResult->getFeedSubmissionInfoList();
    }
}

?>