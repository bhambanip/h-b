<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 7/4/2018
 * Time: 8:29 PM
 */
include_once('/var/www/html/Scripts/dh/commonPrepends.php');

class newEggItem
{
    var $SellerPartNumber;
    var $Manufacturer;
    var $UPC;
    var $SellingPrice;
    var $Shipping;
    var $Inventory;
    var $ItemCondition;
    var $PacksOrSets;
    var $ManufacturerPartNumberOrISBN;

    public function __construct($item, $newEggManufacturer)
    {
        $manufacturer = $item['Brand'];
        foreach ($newEggManufacturer as $brand) {
            if (strcmp($brand['brand'], $manufacturer) == 0) {
                $manufacturer = $brand['manufacturer'];
            }
        }

        $quantity = $item['Quantity'];
        if (!$item['Status'] || !($item['Weight'] < 61.00) || !$item['Quantity'] || !$item['Price'] || !$item['Upc']) {
            $quantity = 0;
        }
        $price = $item['Price'] * (1 + newEggDefaultMargin / 100);
        $price += newEggShipping;
        $this->SellerPartNumber = $item['Sku'];
        $this->Manufacturer = $manufacturer;
        $this->UPC = $item['Upc'];
        $this->SellingPrice = (string)round($price, 2);
        $this->Shipping = "Free";
        $this->Inventory = (string)$quantity;
        $this->ItemCondition = "New";
        $this->PacksOrSets = "1";
        $this->ManufacturerPartNumberOrISBN = $item['ManufacturerSku'];
    }

}