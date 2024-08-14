<?php

namespace App\Helpers;

use App\Products;

class ExcelOrderModel {
    
    const KEY_ORDER_NUM = '訂單編號';
    const KEY_PRODUCT_SLUG = '產品代號';
    const KEY_QUANTITY = '數量';
    const KEY_NAME = '姓名';
    const KEY_PHONE = '電話';
    const KEY_ADDRESS = '地址';
    const KEY_COMPANY_NAME = '三聯抬頭';
    const KEY_COMPANY_NUM = '統編';
    const KEY_MEMO = '備註';

    public $orderNum;
    public $name;
    public $phone;
    public $address;
    public $companyName;
    public $companyNum;
    public $memo;
    public $products = [];

    public function __construct(Array $row) {
        $this->orderNum = $row[static::KEY_ORDER_NUM];
        $this->name = $row[static::KEY_NAME];
        $this->phone = $row[static::KEY_PHONE];
        $this->address = $row[static::KEY_ADDRESS];
        if (isset($row[static::KEY_COMPANY_NAME])) {
            $this->companyName = $row[static::KEY_COMPANY_NAME];
        }
        if (isset($row[static::KEY_COMPANY_NUM])) {
            $this->companyNum = $row[static::KEY_COMPANY_NUM];
        }
        if (isset($row[static::KEY_MEMO])) {
            $this->memo = $row[static::KEY_MEMO];
        }
        
    }

    public function setItem(Products $product) {
        $this->products[] = $product;
    }

}