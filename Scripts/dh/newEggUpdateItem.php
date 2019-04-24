<?php
/**
 * Created by IntelliJ IDEA.
 * User: programmer
 * Date: 7/8/2018
 * Time: 2:31 PM
 */

include_once('/var/www/html/Scripts/dh/commonPrepends.php');

class newEggUpdateItem
{
    var $SellerPartNumber;
    var $Inventory;
    var $SellingPrice;
    var $ActivationMark;

    /**
     * newEggUpdateItem constructor.
     */
    public function __construct($item, $price)
    {
        if (($item['NEMinPrice'] > 0) && ($item['NEMinPrice'] > $price)) {
            $price = ($price + $item['NEMinPrice']) / 2;
        } elseif ($item['NELowestPrice']) {
            $price += 50;
        }
        
        $quantity = $item['Quantity'];
        if (!$item['Status'] || !($item['Weight'] < 61.00) || !$item['Quantity'] || !$item['Price'] || !$item['Upc']) {
            $quantity = 0;
        }
        $this->SellerPartNumber = $item['Sku'];
        $this->Inventory = (string)$quantity;
        $this->SellingPrice = (string)round($price,2);
        $this->ActivationMark = $quantity ? "True" : "False";
    }
}