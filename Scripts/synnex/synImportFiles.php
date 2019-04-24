#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/10/2018
 * Time: 7:31 PM
 */

include_once('synPrepends.php');
include_once('../dh/commonPrepends.php');
// connect and login to FTP server
$ftp_conn = ftp_connect(synFtp) or die("Could not connect to FTP");
$login = ftp_login($ftp_conn, synUser, synPassword);
ftp_pasv($ftp_conn, TRUE);

// download server file
if (ftp_get($ftp_conn, synZipFile, synZipFile, FTP_BINARY)) {
    echo "Successfully written to " . synZipFile;
} else {
    echo "Error downloading " . synZipFile;
}


$memory = ini_set('memory_limit', '16192M');
unZip(synZipFile);


function unZip($synFIle)
{
    $zip = zip_open($synFIle);
    if ($zip) {
        while ($zip_entry = zip_read($zip)) {
            $fp = fopen(zip_entry_name($zip_entry), "w");
            if (zip_entry_open($zip, $zip_entry, "r")) {
                $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                fwrite($fp, "$buf");
                zip_entry_close($zip_entry);
                fclose($fp);
            }
        }
        zip_close($zip);
    }
}

// Create Local File
$localFileToWrite = fopen(synLocalFile, "w");

$file = fopen(synFile, "r");
while (($getData = fgetcsv($file, 1000000, "~")) !== FALSE) {
    $getData = array_map('trim', $getData);
    $realQuantity = intval($getData[synColumnQuantity]) - intval($getData[synColumnManufacturerQuantity]);
    if ((intval($realQuantity) > minQuantity) && (intval($getData[synColumnQuantity]) != 9999)
        && ($getData[synAvailabilityFlag] == 'A')) {
        $LocalRowToWrite = array();
        $LocalRowToWrite = $getData;
        fputcsv($localFileToWrite, $LocalRowToWrite, ",");
    }
}
fclose($localFileToWrite);
fclose($file);


$query = "update " . synItemList . " set Quantity = 0";
$result = mysqli_query($conn, $query);


if ($updateNeeded) {
    $query = "update " . synItemList . " set Status = 0";
    $result = mysqli_query($conn, $query);

    $file = fopen(synLocalFile, "r");
    while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
        $getData = array_map('trim', $getData);
        $sku = "";
        if (isset($getData[synColumnSku])) {
            $sku = mysqli_real_escape_string($conn, 'syn' . $getData[synColumnSku]);
        }

        $upc = "";
        if (isset($getData[synColumnUpc])) {
            $upc = mysqli_real_escape_string($conn, $getData[synColumnUpc]);
        }

        $subCategoryNo = "";
        if (isset($getData[synColumnSubCategoryNo])) {
            $subCategoryNo = mysqli_real_escape_string($conn, $getData[synColumnSubCategoryNo]);
        }

        $brand = "";
        if (isset($getData[synColumnBrand])) {
            $brand = mysqli_real_escape_string($conn, $getData[synColumnBrand]);
        }

        $price = "";
        if (isset($getData[synColumnPrice])) {
            $price = mysqli_real_escape_string($conn, $getData[synColumnPrice]);
        }

        $shortDesc = "";
        if (isset($getData[synColumnShortDesc])) {
            $shortDesc = mysqli_real_escape_string($conn, $getData[synColumnShortDesc]);
        }

        $longDesc = "";
        if (isset($getData[synColumnLongDesc])) {
            $longDesc = mysqli_real_escape_string($conn, $getData[synColumnLongDesc]);
        }

        $weight = "";
        if (isset($getData[synColumnWeight])) {
            $weight = mysqli_real_escape_string($conn, $getData[synColumnWeight]);
        }

        $manufacturerSku = "";
        if (isset($getData[synColumnManufacturerSku])) {
            $manufacturerSku = mysqli_real_escape_string($conn, $getData[synColumnManufacturerSku]);
        }

        $length = "";
        if (isset($getData[synColumnLength])) {
            $length = mysqli_real_escape_string($conn, $getData[synColumnLength]);
        }

        $width = "";
        if (isset($getData[synColumnWidth])) {
            $width = mysqli_real_escape_string($conn, $getData[synColumnWidth]);
        }

        $height = "";
        if (isset($getData[synColumnHeight])) {
            $height = mysqli_real_escape_string($conn, $getData[synColumnHeight]);
        }

        $quantity = "";
        if (isset($getData[synColumnQuantity])) {
            $realQuantity = intval($getData[synColumnQuantity]) - intval($getData[synColumnManufacturerQuantity]);
            $quantity = mysqli_real_escape_string($conn, $realQuantity);
        }

        $status = 1;

        if (!empty($sku)) {
            $query = "SELECT Id FROM " . synItemList . " WHERE Sku = '" . $sku . "'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $query = "UPDATE " . synItemList . " SET Upc = '" . $upc . "', ShortDesc = '" . $shortDesc . "', LongDesc = '" . $longDesc . "',
                        SubCategoryNo = '" . $subCategoryNo . "', Brand = '" . $brand . "', Price = '" . $price . "',
                        Weight = '" . $weight . "', ManufacturerSku = '" . $manufacturerSku . "', Status = '" . $status . "', Quantity = '" . $quantity . "',
                        Length = '" . $length . "', Width = '" . $width . "', Height = '" . $height . "' WHERE Id = '" . $row['Id'] . "'";
                    $updateQuery = mysqli_query($conn, $query);
                }
            } else {
                $query = "INSERT INTO " . synItemList . " (Sku, Upc, SubCategoryNo, Brand, Price, ShortDesc, LongDesc, Weight, ManufacturerSku, Status,
                    Length, Width, Height, Quantity)
                    VALUES ('" . $sku . "', '" . $upc . "', '" . $subCategoryNo . "', '" . $brand . "', '" . $price . "', '" . $shortDesc . "',
                    '" . $longDesc . "', '" . $weight . "', '" . $manufacturerSku . "', '" . $status . "',
                    '" . $length . "', '" . $width . "', '" . $height . "', '" . $quantity . "')";
                $insertQuery = mysqli_query($conn, $query);
            }
        }
    }
    fclose($file);
}

// Quantity list
$file = fopen(synLocalFile, "r");
while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
    $getData = array_map('trim', $getData);
    if (intval($getData[synColumnQuantity]) > minQuantity) {
        $sku = "";
        if (isset($getData[synColumnSku])) {
            $sku = mysqli_real_escape_string($conn, 'syn' . $getData[synColumnSku]);
        }
        $quantity = "";
        if (isset($getData[synColumnQuantity])) {
            $realQuantity = intval($getData[synColumnQuantity]) - intval($getData[synColumnManufacturerQuantity]);
            $quantity = mysqli_real_escape_string($conn, $realQuantity);
        }

        if (!empty($sku)) {
            $query = "SELECT Id FROM " . synItemList . " WHERE Sku = '" . $sku . "'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $query = "UPDATE " . synItemList . " SET Quantity = '" . $quantity . "' WHERE Id = '" . $row['Id'] . "'";
                    $updateQuery = mysqli_query($conn, $query);
                }
            }
        }
    }
}
fclose($file);

foreach ($excludeBrands as $excludeBrand) {
    $query = "update " . synItemList . " set status = 0 WHERE Brand like '%" . $excludeBrand . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($excludeUPCs as $excludeUPC) {
    $query = "update " . synItemList . " set status = 0 WHERE Upc like '" . $excludeUPC . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($synExcludeSkus as $excludeSkus) {
    $query = "update " . synItemList . " set status = 0 WHERE Sku like '" . $excludeSkus . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($synExcludeSkusRegulation as $excludeSkus) {
    $query = "update " . synItemList . " set status = 0, Regulation = 0 WHERE Sku like '" . $excludeSkus . "%'";
    $result = mysqli_query($conn, $query);
}

$refurbQuery = "update " . synItemList . " set status = 0 WHERE LongDesc like '%refurb%'";
$result = mysqli_query($conn, $refurbQuery);

include_once('createSynAmzFile.php');
include_once('synNewEgg.php');
include_once('synUpdateNewEgg.php');
mysqli_close($conn);
?>