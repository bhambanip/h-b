#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/10/2018
 * Time: 7:31 PM
 */

include_once('/var/www/html/Scripts/asi/asiPrepends.php');
include_once('/var/www/html/Scripts/dh/commonPrepends.php');
// connect and login to FTP server
$ftp_conn = ftp_connect(asiFtp) or die("Could not connect to FTP");
$login = ftp_login($ftp_conn, asiUser, asiPassword);
ftp_pasv($ftp_conn, TRUE);


// download item file
if (ftp_get($ftp_conn, "/var/www/html/Scripts/asi/" . asiItemFile, asiItemFile, FTP_ASCII)) {
    echo "Successfully written to " . asiItemFile;
} else {
    echo "Error downloading " . asiItemFile;
}


$query = "update asiItemList set Quantity = 0";
$result = mysqli_query($conn, $query);

if (file_exists("/var/www/html/Scripts/asi/" . asiItemFile)) {
    if ($updateNeeded) {
        $file = fopen("/var/www/html/Scripts/asi/" . asiItemFile, "r");
        while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
            $getData = array_map('trim', $getData);
            $totalQuantity = intval($getData[asiColumnQuantityVancouver]) + intval($getData[asiColumnQuantityToronto]);
            if (($totalQuantity > minQuantityZero) && (strcmp($getData[asiColumnStatus], "ACTIVE") == 0)) {
                $sku = "";
                if (isset($getData[asiColumnSku])) {
                    $sku = mysqli_real_escape_string($conn, $getData[asiColumnSku]);
                }

                $upc = "";
                if (isset($getData[asiColumnUpc])) {
                    $upc = mysqli_real_escape_string($conn, $getData[asiColumnUpc]);
                }

                $subCategoryNo = "";
                if (isset($getData[asiColumnSubCategoryNo])) {
                    $subCategoryNo = mysqli_real_escape_string($conn, $getData[asiColumnSubCategoryNo]);
                }

                $brand = "";
                if (isset($getData[asiColumnBrand])) {
                    $brand = mysqli_real_escape_string($conn, $getData[asiColumnBrand]);
                }

                $price = "";
                if (isset($getData[asiColumnPrice])) {
                    $price = mysqli_real_escape_string($conn, $getData[asiColumnPrice]);
                }

                $shortDesc = "";
                if (isset($getData[asiColumnShortDesc])) {
                    $shortDesc = mysqli_real_escape_string($conn, $getData[asiColumnShortDesc]);
                }

                $longDesc = "";
                if (isset($getData[asiColumnLongDesc])) {
                    $longDesc = mysqli_real_escape_string($conn, $getData[asiColumnLongDesc]);
                }

                $weight = "";
                if (isset($getData[asiColumnWeight])) {
                    $weight = mysqli_real_escape_string($conn, $getData[asiColumnWeight]);
                }

                $quantity = "";
                if (isset($totalQuantity)) {
                    $quantity = mysqli_real_escape_string($conn, $totalQuantity);
                }

                $categoryNo = "";
                if (isset($getData[asiColumnCategoryNo])) {
                    $categoryNo = mysqli_real_escape_string($conn, $getData[asiColumnCategoryNo]);
                }

                $manufacturerSku = "";
                if (isset($getData[asiColumnManufacturerSku])) {
                    $manufacturerSku = mysqli_real_escape_string($conn, $getData[asiColumnManufacturerSku]);
                }

                if (!empty($sku)) {
                    $query = "SELECT Id FROM asiItemList WHERE Sku = '" . $sku . "'";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $query = "UPDATE asiItemList SET Upc = '" . $upc . "', ShortDesc = '" . $shortDesc . "', LongDesc = '" . $longDesc . "', 
                        SubCategoryNo = '" . $subCategoryNo . "', Brand = '" . $brand . "', Quantity = '" . $quantity . "', Price = '" . $price . "', 
                        Weight = '" . $weight . "', CategoryNo = '" . $categoryNo . "', ManufacturerSku = '" . $manufacturerSku . "',  Status = 1, Regulation = 1 WHERE Id = '" . $row['Id'] . "'";
                            $updateQuery = mysqli_query($conn, $query);
                        }
                    } else {
                        $query = "INSERT INTO asiItemList (Sku, Upc, SubCategoryNo, Brand, Price, ShortDesc, LongDesc, Weight, Quantity, CategoryNo, ManufacturerSku) 
                    VALUES ('" . $sku . "', '" . $upc . "', '" . $subCategoryNo . "', '" . $brand . "', '" . $price . "', '" . $shortDesc . "',
                     '" . $longDesc . "', '" . $weight . "', '" . $quantity . "', '" . $categoryNo . "', '" . $manufacturerSku . "')";
                        $insertQuery = mysqli_query($conn, $query);
                    }
                }
            }
        }
    } else {
        $file = fopen("/var/www/html/Scripts/asi/" . asiItemFile, "r");
        while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
            $getData = array_map('trim', $getData);
            $totalQuantity = intval($getData[asiColumnQuantityVancouver]) + intval($getData[asiColumnQuantityToronto]);
            if (($totalQuantity > minQuantityZero) && (strcmp($getData[asiColumnStatus], "ACTIVE") == 0)) {

                $sku = "";
                if (isset($getData[asiColumnSku])) {
                    $sku = mysqli_real_escape_string($conn, $getData[asiColumnSku]);
                }

                $quantity = "";
                if (isset($totalQuantity)) {
                    $quantity = mysqli_real_escape_string($conn, $totalQuantity);
                }

                $price = "";
                if (isset($getData[asiColumnPrice])) {
                    $price = mysqli_real_escape_string($conn, $getData[asiColumnPrice]);
                }

                if (!empty($sku)) {
                    $query = "SELECT Id FROM asiItemList WHERE Sku = '" . $sku . "'";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $query = "UPDATE asiItemList SET Price = '" . $price . "', Quantity = '" . $quantity . "' WHERE Id = '" . $row['Id'] . "'";
                            $updateQuery = mysqli_query($conn, $query);
                        }
                    }
                }
            }
        }
    }
    fclose($file);

    foreach ($excludeBrands as $excludeBrand) {
        $query = "update asiItemList set status = 0 WHERE Brand like '" . $excludeBrand . "%'";
        $result = mysqli_query($conn, $query);
    }

    foreach ($excludeUPCs as $excludeUPC) {
        $query = "update asiItemList set status = 0 WHERE Upc like '" . $excludeUPC . "%'";
        $result = mysqli_query($conn, $query);
    }

    foreach ($asiExcludeSkus as $excludeSkus) {
        $query = "update asiItemList set status = 0 WHERE Sku like '" . $excludeSkus . "%'";
        $result = mysqli_query($conn, $query);
    }

    foreach ($asiExcludeSkusRegulation as $excludeSkus) {
        $query = "update asiItemList set status = 0, Regulation = 0 WHERE Sku like '" . $excludeSkus . "%'";
        $result = mysqli_query($conn, $query);
    }
}
include_once('createAsiAmzFile.php');
include_once('asiNewEgg.php');
include_once('asiUpdateNewEgg.php');
mysqli_close($conn);
?>