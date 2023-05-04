<?php
namespace Mft\Shop\Model;

class User extends BaseModel{
    public $table = "users";

    public $columns = [
        "mobile",
        "password",
    ];
}