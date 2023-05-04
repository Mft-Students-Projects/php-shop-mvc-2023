<?php
//require_once "src/Model/Category.php";
$categoryModel = new \Mft\Shop\Model\Category();
$GLOBALS["root_categories"] = $categoryModel->where("`parent_id` IS NULL");

function getSubCategories($parentId){
    $categoryModel = new \Mft\Shop\Model\Category();
    return $categoryModel->where("`parent_id` = ".$parentId);
}

function json($data){
    header("Content-Type:application/json; charset=UTF-8");
    echo json_encode($data);
    die();
}