<?php
namespace Mft\Shop\Controllers;

use Mft\Shop\Model\Product;

class SearchController {

    public function omdb(){

        if(isset($_GET["search"])){
            // create & initialize a curl session
            $curl = curl_init();
            // set our url with curl_setopt()
            curl_setopt($curl, CURLOPT_URL, "http://www.omdbapi.com/?apikey=4678f9a4&s=".$_GET["search"]);
            // return the transfer as a string, also with setopt() curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // curl_exec() executes the started curl session and $output contains the output string
            $output = curl_exec($curl);
            // close curl resource to free up system resources and (deletes the variable made by curl_init)
            header("Content-Type:application/json; charset=UTF-8");
            echo $output;

            curl_close($curl);
        }

    }

    public function index(){
        $dataJson = json_decode(file_get_contents("php://input"),true);
//        echo $dataJson["keyword"];
        $productModel = new Product();
        $preparedSql = $productModel->where("`name` LIKE ?");
        $keyword = "%".$dataJson["keyword"]."%";
        $preparedSql->bind_param("s",$keyword);
        $preparedSql->execute();

        header("Content-Type:application/json; charset=UTF-8");
        echo json_encode($preparedSql->get_result()->fetch_all(MYSQLI_ASSOC));
    }
}