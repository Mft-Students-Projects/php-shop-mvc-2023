<?php
namespace Mft\Shop\Controllers;

use Mft\Shop\Model\Product;
use Mft\Shop\Model\Cart;
use Mft\Shop\Model\CartItem;
use Mft\Shop\Model\ProductCategory;

class CartController {
    public function store(){
        if(isset($_GET["product_id"])){

            $productId = $_GET["product_id"];

            $user = $_SESSION["customer"];

            $cartModel = new Cart();

            $resultCart = $cartModel->where("`user_id` = ".$user["id"]." AND `is_order` = 0","*");

            $totalPrice = 0;
            $totalCount = 0;
            $isOrder = 0;

            if($resultCart){

                $cartId = $resultCart[0]["id"];

            }else{

                $preparedSql = $cartModel->create();
//                debug($GLOBALS["connection"]->error);

                $preparedSql->bind_param("siii",$totalPrice,$totalCount,$user["id"],$isOrder);
                $result = $preparedSql->execute();
                if($result){
                    $cartId = $GLOBALS["connection"]->insert_id;
                }

            }

            $productModel = new Product();
            $product = $productModel->findById($productId);

//            $product["price"]

            $preparedSql = $cartModel->update();
            if($resultCart){
                $totalPrice = $resultCart[0]["total_price"] + $product["price"];
                $totalCount =$resultCart[0]["total_count"]+1;
                $preparedSql->bind_param("siiii",$totalPrice,$totalCount,$user["id"],$isOrder,$cartId);
            }else{
                $count = 1;
                $preparedSql->bind_param("siiii",$product["price"],$count,$user["id"],$isOrder,$cartId);
            }

            $result = $preparedSql->execute();

            $cartItemModel = new CartItem();
            $resultCartItem = $cartItemModel->where("`cart_id` = ".$cartId." AND `product_id` = ".$product["id"]);
            
            if($resultCartItem){

                $preparedSql = $cartItemModel->update();

                $count = $resultCartItem[0]["count"] + 1;

                $preparedSql->bind_param("iiisi",$productId,$cartId,$count,$product["price"],$resultCartItem[0]["id"]);

                $preparedSql->execute();

            }else{

                $preparedSql = $cartItemModel->create();

                $count = 1;

                $preparedSql->bind_param("iiis",$productId,$cartId,$count,$product["price"]);

                $preparedSql->execute();

            }

            redirect("/cart");

        }
    }


    public function index(){

        $cartModel = new Cart();

        $cartResult = $cartModel->where("`user_id` = ".$_SESSION["customer"]["id"]." AND `is_order` = 0");

        if($cartResult){

           $cartResult = $cartResult[0];

           $cartItemModel = new CartItem();

           $cartItemResult = $cartItemModel->query("SELECT *,`cart_items`.`id` as cart_item_id FROM `cart_items` INNER JOIN `products` ON `cart_items`.`product_id` = `products`.`id` WHERE `cart_items`.`cart_id` = ".$cartResult["id"]);
//            "`cart_id` = ".$cartResult["id"]
        }

        require_once "src/views/cart.php";
    }

    public function delete(){

        if(isset($_GET["cart_item_id"])){

            $cartItemModel = new CartItem();
            $cartItemResult = $cartItemModel->findById($_GET["cart_item_id"]); // price and count

            $cartModel = new Cart();
            $cartResult = $cartModel->findById($cartItemResult["cart_id"]);

            $preparedSql = $cartModel->update();

            $total_price = $cartResult["total_price"] - ($cartItemResult["unit_price"] * $cartItemResult["count"]);
            $total_count = $cartResult["total_count"] - ($cartItemResult["count"]);
            $is_order = 0;

            $preparedSql->bind_param("iiiii",$total_price,$total_count,$_SESSION["customer"]["id"],$is_order,$cartResult["id"]);
            $preparedSql->execute();


            $cartItemModel->delete($_GET["cart_item_id"]);


            redirect("/cart");


        }

    }


    public function update(){

        if(isset($_GET["cart_item_id"]) && isset($_GET["dir"])){

            $cartItemModel = new CartItem();
            $cartItemResult = $cartItemModel->findById($_GET["cart_item_id"]); // price and count

            $cartModel = new Cart();
            $cartResult = $cartModel->findById($cartItemResult["cart_id"]); // price and count

            $preparedSql = $cartItemModel->update();

            if($_GET["dir"] == "minus" && $cartItemResult["count"] == 1){
                echo json_encode([
                    "error"=>"کمترین تعداد یک عدد است"
                ]);
            }

            $newCount = $_GET["dir"] == "plus" ? $cartItemResult["count"] + 1 : $cartItemResult["count"] - 1;

            $preparedSql->bind_param("iiiii",
                $cartItemResult["product_id"],
                $cartItemResult["cart_id"],
                $newCount,
                $cartItemResult["unit_price"],
                $cartItemResult["id"]
            );

            $preparedSql->execute();


            $preparedSql = $cartModel->update();

            $newTotalCount = $_GET["dir"] == "plus" ? $cartResult["total_count"] + 1 : $cartResult["total_count"] - 1;
            $newTotalPrice = $_GET["dir"] == "plus" ?
                (int)$cartResult["total_price"] + (int)$cartItemResult["unit_price"] :
                (int)$cartResult["total_price"] - (int)$cartItemResult["unit_price"];


//            var_dump([$newTotalCount,$newTotalPrice]);
//            die();

            $preparedSql->bind_param("iiiii",
                $newTotalPrice,
                $newTotalCount,
                $cartResult["user_id"],
                $cartResult["is_order"],
                $cartResult["id"]
            );

            $preparedSql->execute();


            echo json_encode([
                "total_count"=>$newTotalCount,
                "total_price"=>$newTotalPrice,
                "count"=>$newCount,
                "unit_price"=>(int)$cartItemResult["unit_price"],
            ]);


        }

    }


    function pay(){
        $cartModel = new Cart();
        $cartResult = $cartModel->where('`user_id` = '.$_SESSION["customer"]["id"]." AND `is_order` = 0"); // price and count

        if($cartResult && isset($cartResult[0])){
            $_SESSION["price"] = $cartResult[0]["total_price"];
            redirect("/payment/go");
        }

    }

}