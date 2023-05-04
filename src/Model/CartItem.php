<?php
namespace Mft\Shop\Model;

class CartItem extends BaseModel{
    public $table = "cart_items";

    public $columns = [
        "product_id",
        "cart_id",
        "count",
        "unit_price"
    ];
}