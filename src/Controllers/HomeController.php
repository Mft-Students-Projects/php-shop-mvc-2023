<?php
namespace Mft\Shop\Controllers;

use Gumlet\ImageResize;
use Kavenegar\KavenegarApi;
use Mft\Shop\Model\Product;

class HomeController {

    function index(){
//        var_dump($GLOBALS["categories"]);
//        die();

//        $kave = new KavenegarApi("Api Key");

        $image = new ImageResize("media/06.jpg");
        $image->resizeToHeight(500);
        $image->crop(100,100);
        $image->save('media/06-500px.jpg');


        $productModel = new Product();
        $products = $productModel->getAll("ORDER BY `id` DESC LIMIT 20");
        include "src/views/index.php";
    }

}