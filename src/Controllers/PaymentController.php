<?php
namespace Mft\Shop\Controllers;

use Mft\Shop\Libs\Zarinpal;
use Mft\Shop\Model\Cart;

class PaymentController {

    function go(){

        if(!isset($_SESSION["price"])){
            die("403");
        }

        $MerchantID 	= "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
        $Amount 		= $_SESSION["price"];
        $Description 	= "تراکنش زرین پال";
        $Email 			= "";
        $Mobile 		= "";
        $CallbackURL 	= BASE_URL."/payment/verify";
        $ZarinGate 		= false;
        $SandBox 		= true; // زمین بازی

//        unset($_SESSION["price"]);

        $zp 	= new Zarinpal();
        $result = $zp->request($MerchantID, $Amount, $Description, $Email, $Mobile, $CallbackURL, $SandBox, $ZarinGate);


        if (isset($result["Status"]) && $result["Status"] == 100)
        {
            // Success and redirect to pay
            $zp->redirect($result["StartPay"]);
        } else {
            // error
            echo "خطا در ایجاد تراکنش";
            echo "<br />کد خطا : ". $result["Status"];
            echo "<br />تفسیر و علت خطا : ". $result["Message"];
        }
    }

    function verify(){

        $MerchantID 	= "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
        $Amount 		= $_SESSION["price"];
        $ZarinGate 		= false;
        $SandBox 		= true;

        unset($_SESSION["price"]);

        $zp 	= new Zarinpal();
        $result = $zp->verify($MerchantID, $Amount, $SandBox, $ZarinGate);

        if (isset($result["Status"]) && $result["Status"] == 100)
        {
            // update cart
            $cartModel = new Cart();
            $cartResult = $cartModel->where('`user_id` = '.$_SESSION["customer"]["id"]." AND `is_order` = 0"); // price and count

            $preparedSql = $cartModel->update();

            $is_order = 1;

            $preparedSql->bind_param("iiiii",
                $cartResult[0]["total_price"],
                $cartResult[0]["total_count"],
                $cartResult[0]["user_id"],
                $is_order,
                $cartResult[0]["id"]
            );

            $preparedSql->execute();

            if(isset($_SESSION["customer"]["email"]) && !empty($_SESSION["customer"]["email"])){
                mail(
                    $_SESSION["customer"]["email"],
                    "ثبت سفارش : ".$cartResult[0]["id"],
                    $_SESSION["customer"]["name"]." عزیز , سفارش شما با شماره پیگیری ".$cartResult[0]["id"]." با موفقیت ثبت شد , فروشگاه تست"
                );
            }

        }


        require_once "src/views/payment.php";



    }


}