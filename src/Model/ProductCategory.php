<?php
namespace Mft\Shop\Model;

class ProductCategory extends BaseModel{
    public $table = "product_category";

    public $columns = [
        "product_id",
        "category_id",
    ];
}