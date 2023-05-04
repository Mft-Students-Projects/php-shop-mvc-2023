<?php
namespace Mft\Shop\Model;

class Category extends BaseModel{
    public $table = "categories";

    public $columns = [
        "name",
        "enname",
        "parent_id"
    ];
}