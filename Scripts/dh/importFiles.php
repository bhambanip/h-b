#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/10/2018
 * Time: 7:31 PM
 */

include_once('/var/www/html/Scripts/dh/prepends.php');
include_once('/var/www/html/Scripts/dh/commonPrepends.php');
// connect and login to FTP server
$ftp_server = dhFtp;
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, dhUser, dhPassword);
ftp_pasv($ftp_conn, TRUE);

// download server file
foreach ($dhServerFiles as $dhServerFile) {
    if (ftp_get($ftp_conn, "/var/www/html/Scripts/dh/" . dhPrefix . $dhServerFile . importFileExt, $dhServerFile, FTP_ASCII)) {
        echo "Successfully written to " . $dhServerFile . importFileExt;
    } else {
        echo "Error downloading " . $dhServerFile;
    }
}

// close connection
ftp_close($ftp_conn);

$query = "update dhItemList set Quantity = 0";
$result = mysqli_query($conn, $query);

if ($updateNeeded) {
    $file = fopen("/var/www/html/Scripts/dh/" . dhPrefix . $dhServerFiles[0] . importFileExt, "r");
    while (($getData = fgetcsv($file, 1000000, dhServerFileDelimiter)) !== FALSE) {
        $getData = array_map('trim', $getData);
        if (intval($getData[dhColumnQuantity]) > minQuantityZero) {
            $sku = "";
            if (isset($getData[dhColumnSku])) {
                $sku = mysqli_real_escape_string($conn, $getData[dhColumnSku]);
            }

            $quantity = "";
            if (isset($getData[dhColumnQuantity])) {
                $quantity = mysqli_real_escape_string($conn, $getData[dhColumnQuantity]);
            }

            $upc = "";
            if (isset($getData[dhColumnUpc])) {
                $upc = mysqli_real_escape_string($conn, $getData[dhColumnUpc]);
            }

            $subCategoryNo = "";
            if (isset($getData[dhColumnSubCategoryNo])) {
                $subCategoryNo = mysqli_real_escape_string($conn, $getData[dhColumnSubCategoryNo]);
            }

            $brand = "";
            if (isset($getData[dhColumnBrand])) {
                $brand = mysqli_real_escape_string($conn, $getData[dhColumnBrand]);
            }

            $price = "";
            if (isset($getData[dhColumnPrice])) {
                $price = mysqli_real_escape_string($conn, $getData[dhColumnPrice]);
            }

            $shortDesc = "";
            if (isset($getData[dhColumnShortDesc])) {
                $shortDesc = mysqli_real_escape_string($conn, $getData[dhColumnShortDesc]);
            }

            $longDesc = "";
            if (isset($getData[dhColumnLongDesc])) {
                $longDesc = mysqli_real_escape_string($conn, $getData[dhColumnLongDesc]);
            }

            $weight = "";
            if (isset($getData[dhColumnWeight])) {
                $weight = mysqli_real_escape_string($conn, $getData[dhColumnWeight]);
            }

            $manufacturerSku = "";
            if (isset($getData[5])) {
                $manufacturerSku = mysqli_real_escape_string($conn, $getData[5]);
            }

            if (!empty($sku)) {
                $query = "SELECT Id FROM dhItemList WHERE Sku = '" . $sku . "'";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $query = "UPDATE dhItemList SET Upc = '" . $upc . "', ShortDesc = '" . $shortDesc . "', LongDesc = '" . $longDesc . "', 
                        SubCategoryNo = '" . $subCategoryNo . "', Brand = '" . $brand . "', Price = '" . $price . "', Quantity = '" . $quantity . "', 
                        Weight = '" . $weight . "', ManufacturerSku = '" . $manufacturerSku . "', Status = 1 WHERE Id = '" . $row['Id'] . "'";
                        $updateQuery = mysqli_query($conn, $query);
                    }
                } else {
                    $query = "INSERT INTO dhItemList (Sku, Upc, SubCategoryNo, Brand, Price, ShortDesc, LongDesc, Quantity, Weight, ManufacturerSku) 
                    VALUES ('" . $sku . "', '" . $upc . "', '" . $subCategoryNo . "', '" . $brand . "', '" . $price . "', 
                     '" . $shortDesc . "', '" . $longDesc . "', '" . $quantity . "', '" . $weight . "', '" . $manufacturerSku . "')";
                    $insertQuery = mysqli_query($conn, $query);
                }
            }
        }
    }
} else {
    $file = fopen("/var/www/html/Scripts/dh/" . dhPrefix . $dhServerFiles[0] . importFileExt, "r");
    while (($getData = fgetcsv($file, 1000000, dhServerFileDelimiter)) !== FALSE) {
        $getData = array_map('trim', $getData);
        if (intval($getData[dhColumnQuantity]) > minQuantityZero) {
            $sku = "";
            if (isset($getData[dhColumnSku])) {
                $sku = mysqli_real_escape_string($conn, $getData[dhColumnSku]);
            }

            $quantity = "";
            if (isset($getData[dhColumnQuantity])) {
                $quantity = mysqli_real_escape_string($conn, $getData[dhColumnQuantity]);
            }

            $price = "";
            if (isset($getData[dhColumnPrice])) {
                $price = mysqli_real_escape_string($conn, $getData[dhColumnPrice]);
            }

            if (!empty($sku)) {
                $query = "SELECT Id FROM dhItemList WHERE Sku = '" . $sku . "'";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $query = "UPDATE dhItemList SET Price = '" . $price . "', Quantity = '" . $quantity . "' WHERE Id = '" . $row['Id'] . "'";
                        $updateQuery = mysqli_query($conn, $query);
                    }
                }
            }
        }
    }
}
fclose($file);


// Category list
if ($updateNeeded) {
    $file = fopen("/var/www/html/Scripts/dh/" . dhPrefix . $dhServerFiles[1] . importFileExt, "r");
    while (($getData = fgetcsv($file, 1000000, dhServerFileDelimiter)) !== FALSE) {
        $getData = array_map('trim', $getData);
        $categoryNo = "";
        if (isset($getData[0])) {
            $categoryNo = mysqli_real_escape_string($conn, $getData[0]);
        }

        $categoryName = "";
        if (isset($getData[1])) {
            $categoryName = mysqli_real_escape_string($conn, $getData[1]);
        }

        $subCategoryNo = "";
        if (isset($getData[2])) {
            $subCategoryNo = mysqli_real_escape_string($conn, $getData[2]);
        }

        $subCategoryName = "";
        if (isset($getData[3])) {
            $subCategoryName = mysqli_real_escape_string($conn, $getData[3]);
        }

        if (!empty($subCategoryNo)) {
            $query = "SELECT Id FROM dhCatList WHERE SubCategoryNo = '" . $subCategoryNo . "'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $query = "UPDATE dhCatList SET CategoryNo = '" . $categoryNo . "', CategoryName = '" . $categoryName . "', SubCategoryName = '" . $subCategoryName . "' 
                         WHERE Id = '" . $row['Id'] . "'";
                    $updateQuery = mysqli_query($conn, $query);
                }
            } else {
                $query = "INSERT INTO dhCatList (CategoryNo, CategoryName, SubCategoryNo, SubCategoryName) 
                    VALUES ('" . $categoryNo . "', '" . $categoryName . "', '" . $subCategoryNo . "', '" . $subCategoryName . "')";
                $insertQuery = mysqli_query($conn, $query);
            }
        }
    }
    fclose($file);
}
foreach ($excludeBrands as $excludeBrand) {
    $query = "update dhItemList set status = 0 WHERE Brand like '" . $excludeBrand . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($excludeUPCs as $excludeUPC) {
    $query = "update dhItemList set status = 0 WHERE Upc like '" . $excludeUPC . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($dhExcludeSkus as $excludeSkus) {
    $query = "update dhItemList set status = 0 WHERE Sku like '" . $excludeSkus . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($dhExcludeSkusRegulation as $excludeSkus) {
    $query = "update dhItemList set status = 0, Regulation = 0 WHERE Sku like '" . $excludeSkus . "%'";
    $result = mysqli_query($conn, $query);
}


include_once('createAmzFile.php');
include_once('dhNewEgg.php');
include_once('dhUpdateNewEgg.php');

mysqli_close($conn);
?>