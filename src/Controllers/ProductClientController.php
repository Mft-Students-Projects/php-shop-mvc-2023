<?php
namespace Mft\Shop\Controllers;

use Mft\Shop\Model\Product;
use Mft\Shop\Model\Category;
use Mft\Shop\Model\ProductCategory;

class ProductClientController {
    public function show(){
        if(!isset($_GET["id"])){
            die("404");
        }

        $productModel = new Product();
        $product = $productModel->findById($_GET["id"]);

        if(!$product){
            die("404");
        }

        include "src/views/product.php";
    }


    public function showApi(){
        if(!isset($_GET["id"])){
            die("404");
        }

        $productModel = new Product();
        $product = $productModel->findById($_GET["id"]);

        // api response

        if(!$product){
            // errro code 404
            http_response_code(404);
            json([
                "status"=>404,
                "message"=>"Not Found !"
            ]);
        }

        json($product);

    }
}