#!/usr/bin/php -q
<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 6/10/2018
 * Time: 7:31 PM
 */

include_once('/var/www/html/Scripts/ingram/ingramPrepends.php');
include_once('/var/www/html/Scripts/dh/commonPrepends.php');
// connect and login to FTP server
$ftp_conn = ftp_connect(ingramFtp) or die("Could not connect to FTP");
$login = ftp_login($ftp_conn, ingramUser, ingramPassword);
ftp_pasv($ftp_conn, TRUE);

// download server file
if (ftp_get($ftp_conn, "/var/www/html/Scripts/ingram/" . ingramPriceZipFile, ingramPriceDirectory . ingramPriceZipFile, FTP_BINARY)) {
    echo "Successfully written to " . ingramPriceZipFile;
} else {
    echo "Error downloading " . ingramPriceZipFile;
}

// download server file
if (ftp_get($ftp_conn, "/var/www/html/Scripts/ingram/" . ingramQuantityFile, ingramQuantityDirectory . ingramQuantityFile, FTP_ASCII)) {
    echo "Successfully written to " . ingramQuantityFile;
} else {
    echo "Error downloading " . ingramQuantityFile;
}

// download Category file
if (ftp_get($ftp_conn, "/var/www/html/Scripts/ingram/" . ingramCatFile, ingramCatDirectory . ingramCatFile, FTP_ASCII)) {
    echo "Successfully written to " . ingramCatFile;
} else {
    echo "Error downloading " . ingramCatFile;
}

// close connection
ftp_close($ftp_conn);

$memory = ini_set('memory_limit', '8192M');
unZip("/var/www/html/Scripts/ingram/" . ingramPriceZipFile);


function unZip($ingramFIle)
{
    $zip = zip_open($ingramFIle);
    if ($zip) {
        while ($zip_entry = zip_read($zip)) {
            $fp = fopen("/var/www/html/Scripts/ingram/" . zip_entry_name($zip_entry), "w");
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

$query = "update ingramItemList set Quantity = 0";
$result = mysqli_query($conn, $query);


if ($updateNeeded) {
   /* $query = "update ingramItemList set Status = 0";
    $result = mysqli_query($conn, $query);*/

    $file = fopen("/var/www/html/Scripts/ingram/" . ingramPriceFile, "r");
    while (($getData = fgetcsv($file, 1000000, "\t")) !== FALSE) {
        $getData = array_map('trim', $getData);
        if ($getData[ingramAvailabilityFlag] == 'Y') {
            $sku = "";
            if (isset($getData[ingramColumnSku])) {
                $sku = mysqli_real_escape_string($conn, $getData[ingramColumnSku]);
            }

            $upc = "";
            if (isset($getData[ingramColumnUpc])) {
                $upc = mysqli_real_escape_string($conn, $getData[ingramColumnUpc]);
            }

            $subCategoryNo = "";
            if (isset($getData[ingramColumnSubCategoryNo])) {
                $cpuCode = mysqli_real_escape_string($conn, $getData[18]);
                $subCategoryTempNo = mysqli_real_escape_string($conn, $getData[ingramColumnSubCategoryNo]);
                $subCategoryNo = $subCategoryTempNo . $cpuCode;
            }

            $brand = "";
            if (isset($getData[ingramColumnBrand])) {
                $brand = mysqli_real_escape_string($conn, $getData[ingramColumnBrand]);
            }

            $price = "";
            if (isset($getData[ingramColumnPrice])) {
                $price = mysqli_real_escape_string($conn, $getData[ingramColumnPrice]);
            }

            $shortDesc = "";
            if (isset($getData[ingramColumnShortDesc])) {
                $shortDesc = mysqli_real_escape_string($conn, $getData[ingramColumnShortDesc]);
            }

            $longDesc = "";
            if (isset($getData[ingramColumnLongDesc])) {
                $longDesc = mysqli_real_escape_string($conn, $getData[ingramColumnLongDesc]);
            }

            $weight = "";
            if (isset($getData[ingramColumnWeight])) {
                $weight = mysqli_real_escape_string($conn, $getData[ingramColumnWeight]);
            }

            $manufacturerSku = "";
            if (isset($getData[7])) {
                $manufacturerSku = mysqli_real_escape_string($conn, $getData[7]);
            }

            $length = "";
            if (isset($getData[10])) {
                $length = mysqli_real_escape_string($conn, $getData[10]);
            }

            $width = "";
            if (isset($getData[11])) {
                $width = mysqli_real_escape_string($conn, $getData[11]);
            }

            $height = "";
            if (isset($getData[12])) {
                $height = mysqli_real_escape_string($conn, $getData[12]);
            }

            $status = 1;

            if (!empty($sku)) {
                $query = "SELECT Id FROM ingramItemList WHERE Sku = '" . $sku . "'";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $query = "UPDATE ingramItemList SET Upc = '" . $upc . "', ShortDesc = '" . $shortDesc . "', LongDesc = '" . $longDesc . "', 
                        SubCategoryNo = '" . $subCategoryNo . "', Brand = '" . $brand . "', Price = '" . $price . "', 
                        Weight = '" . $weight . "', ManufacturerSku = '" . $manufacturerSku . "', Status = '" . $status . "', 
                        Length = '" . $length . "', Width = '" . $width . "', Height = '" . $height . "' WHERE Id = '" . $row['Id'] . "'";
                        $updateQuery = mysqli_query($conn, $query);
                    }
                } else {
                    $query = "INSERT INTO ingramItemList (Sku, Upc, SubCategoryNo, Brand, Price, ShortDesc, LongDesc, Weight, ManufacturerSku, Status,
                    Length, Width, Height) 
                    VALUES ('" . $sku . "', '" . $upc . "', '" . $subCategoryNo . "', '" . $brand . "', '" . $price . "', '" . $shortDesc . "', 
                    '" . $longDesc . "', '" . $weight . "', '" . $manufacturerSku . "', '" . $status . "', 
                    '" . $length . "', '" . $width . "', '" . $height . "')";
                    $insertQuery = mysqli_query($conn, $query);
                }
            }
        }
    }
    fclose($file);

    $file = fopen("/var/www/html/Scripts/ingram/" . ingramCatFile, "r");
    while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
        $getData = array_map('trim', $getData);
        if ($getData[2] == '*') {
            $mainCategoryNo = "";
            if (isset($getData[1])) {
                $mainCategoryNo = mysqli_real_escape_string($conn, $getData[1]);
            }

            $mainCategoryName = "";
            if (isset($getData[0])) {
                $mainCategoryName = mysqli_real_escape_string($conn, $getData[0]);
            }
            continue;
        }
        if ($getData[3] == '*') {
            $categoryNo = "";
            if (isset($getData[1])) {
                $categoryNo1 = mysqli_real_escape_string($conn, $getData[1]);
                $categoryNo2 = mysqli_real_escape_string($conn, $getData[2]);
                $categoryNo = $categoryNo1 . $categoryNo2;
            }

            $categoryName = "";
            if (isset($getData[0])) {
                $categoryName = mysqli_real_escape_string($conn, $getData[0]);
            }
            continue;
        }
        $subCategoryNo = "";
        if (isset($getData[1]) && isset($getData[2])) {
            $subCategoryNo1 = mysqli_real_escape_string($conn, $getData[1]);
            $subCategoryNo2 = mysqli_real_escape_string($conn, $getData[2]);
            $subCategoryNo3 = mysqli_real_escape_string($conn, $getData[3]);
            $subCategoryNo = $subCategoryNo1 . $subCategoryNo2 . $subCategoryNo3;
        }

        $subCategoryName = "";
        if (isset($getData[0])) {
            $subCategoryName = mysqli_real_escape_string($conn, $getData[0]);
        }
        if (!empty($subCategoryNo)) {
            $query = "SELECT Id FROM ingramCatList WHERE SubCategoryNo = '" . $subCategoryNo . "'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $query = "UPDATE ingramCatList SET MainCategoryNo = '" . $mainCategoryNo . "', MainCategoryName = '" . $mainCategoryName . "',
                    CategoryNo = '" . $categoryNo . "', CategoryName = '" . $categoryName . "', SubCategoryName = '" . $subCategoryName . "' 
                    WHERE Id = '" . $row['Id'] . "'";
                    $updateQuery = mysqli_query($conn, $query);
                }
            } else {
                $query = "INSERT INTO ingramCatList (MainCategoryNo, MainCategoryName, CategoryNo, CategoryName, SubCategoryNo, SubCategoryName) 
                    VALUES ('" . $mainCategoryNo . "', '" . $mainCategoryName . "', '" . $categoryNo . "', '" . $categoryName . "', 
                    '" . $subCategoryNo . "', '" . $subCategoryName . "')";
                $insertQuery = mysqli_query($conn, $query);
            }
        }
    }
    fclose($file);
}

// Create Local File
$localFileToWrite = fopen("/var/www/html/Scripts/ingram/" . ingramLocalQuantityFile, "w");

$file = fopen("/var/www/html/Scripts/ingram/" . ingramQuantityFile, "r");
while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
    $getData = array_map('trim', $getData);
    if (intval($getData[1]) > minQuantity) {
        $sku = "";
        if (isset($getData[0])) {
            $sku = mysqli_real_escape_string($conn, $getData[0]);
        }
        $quantity = "";
        if (isset($getData[1])) {
            $quantity = mysqli_real_escape_string($conn, $getData[1]);
        }
        $LocalRowToWrite = array();
        array_push($LocalRowToWrite, $sku, $quantity);
        fputcsv($localFileToWrite, $LocalRowToWrite, ",");
    }
}
fclose($localFileToWrite);
fclose($file);

// Quantity list

$file = fopen("/var/www/html/Scripts/ingram/" . ingramLocalQuantityFile, "r");
while (($getData = fgetcsv($file, 1000000, ",")) !== FALSE) {
    $getData = array_map('trim', $getData);
    if (intval($getData[1]) > minQuantity) {
        $sku = "";
        if (isset($getData[0])) {
            $sku = mysqli_real_escape_string($conn, $getData[0]);
        }
        $quantity = "";
        if (isset($getData[1])) {
            $quantity = mysqli_real_escape_string($conn, $getData[1]);
        }

        if (!empty($sku)) {
            $query = "SELECT Id FROM ingramItemList WHERE Sku = '" . $sku . "'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $query = "UPDATE ingramItemList SET Quantity = '" . $quantity . "' WHERE Id = '" . $row['Id'] . "'";
                    $updateQuery = mysqli_query($conn, $query);
                }
            }
        }
    }
}
fclose($file);

foreach ($excludeBrands as $excludeBrand) {
    $query = "update ingramItemList set status = 0 WHERE Brand like '" . $excludeBrand . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($excludeUPCs as $excludeUPC) {
    $query = "update ingramItemList set status = 0 WHERE Upc like '" . $excludeUPC . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($ingramExcludeSkus as $excludeSkus) {
    $query = "update ingramItemList set status = 0 WHERE Sku like '" . $excludeSkus . "%'";
    $result = mysqli_query($conn, $query);
}

foreach ($ingramExcludeSkusRegulation as $excludeSkus) {
    $query = "update ingramItemList set status = 0, Regulation = 0 WHERE Sku like '" . $excludeSkus . "%'";
    $result = mysqli_query($conn, $query);
}

$query = "update ingramItemList set status = 0 WHERE SubCategoryNo LIKE '%LCD-TV%'";
$result = mysqli_query($conn, $query);


include_once('createIngAmzFile.php');
include_once('ingramNewEgg.php');
include_once('ingramUpdateNewEgg.php');
mysqli_close($conn);
?>