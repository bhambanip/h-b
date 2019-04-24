<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/11/2018
 * Time: 7:57 PM
 */
include_once('synPrepends.php');
include_once('../dh/commonPrepends.php');

$fileToWrite = fopen(synAmzFile, "w");
fputcsv($fileToWrite, $amzFirstRow, "\t");

$result = mysqli_query($conn, "SELECT * FROM " . synItemList . "") or die("database error:" . mysqli_error($connString));

foreach ($result as $row) {
    if ($row['Sku'] && $row['Regulation'] && $row['Status']) {
        $quantity = $row['Quantity'];
        if (!$row['Status'] || !($row['Weight'] < 61.00) || !$row['Quantity'] || !$row['Price'] || !$row['Upc'] || ($row['Price'] < 50.00)) {
            $quantity = 0;
        }
        $rowToWrite = array();
        $price = $row['Price'] + amzShipping;
        $price = changePrice($row['Price'], $price);
        if ($row['AmzFees'] == 0) {
            $margin = amzDefaultMargin;
        } else {
            $margin = $row['AmzFees'];
        }

        if (($row['Height'] > 30.00) || ($row['Length'] > 30.00)) {
            $price += amzOverSize;
        }

        $margin = sendMargin((string)$margin, $changeMargin);
        $price = $price  * (1 + $margin / 100);

        if (($row['AmzMinPrice'] > 0) && ($row['AmzMinPrice'] > $price)) {
            $price = ($price + $row['AmzMinPrice']) / 2;
        } elseif ($row['AmzLowestPrice'] && ($row['AmzMinPrice'] == 0)) {
            $price += 50;
        }

        array_push($rowToWrite, $row['Sku'], $row['Upc'], 3, $price, $quantity, 'a', 20, 2, 11);
        fputcsv($fileToWrite, $rowToWrite, "\t");
    }
}
fclose($fileToWrite);
?>