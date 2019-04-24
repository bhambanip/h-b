<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/11/2018
 * Time: 7:57 PM
 */
include_once('/var/www/html/Scripts/dh/prepends.php');
include_once('/var/www/html/Scripts/dh/commonPrepends.php');

$fileToWrite = fopen(dhAmzFile, "w");
fputcsv($fileToWrite, $amzFirstRow, "\t");

$result = mysqli_query($conn, "SELECT * FROM dhItemList") or die("database error:" . mysqli_error($connString));

foreach ($result as $row) {
    if ($row['Sku'] && $row['Regulation'] && $row['Status']) {
        $quantity = $row['Quantity'];
        if (!$row['Status'] || !($row['Weight'] < 61.00) || !$row['Quantity'] || !$row['Price'] || !$row['Upc'] || ($row['Price'] < 50.00)) {
            $quantity = 0;
        }
        $rowToWrite = array();
        $price = $row['Price'] + amzShipping;
        $price = changePrice($row['Price'], $price);
        if (strpos( $row['ShortDesc'], 'Tower') !== false) {
            $price = $price + 30;
        }
        if ($row['AmzFees'] == 0) {
            $priceQuery = mysqli_query($conn, "SELECT * FROM dhCatList where SubCategoryNo ='" . $row['SubCategoryNo'] . "'");
            if (mysqli_num_rows($priceQuery) > 0) {
                foreach ($priceQuery as $priceResult) {
                    $margin = $priceResult['Margin'] != 0 ? $priceResult['Margin'] : amzDefaultMargin;
                }
            } else {
                $margin = amzDefaultMargin;
            }
        } else {
            $margin = $row['AmzFees'];
        }

        $margin = sendMargin((string)$margin, $changeMargin);
        $price = $price * (1 + $margin / 100);

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