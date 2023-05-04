<?php
namespace Mft\Shop\Model;

class Product extends BaseModel{
    public $table = "products";

    public $columns = [
        "name",
        "enname",
        "price",
        "description",
        "image",
    ];
}