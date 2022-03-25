<?php

require_once "db.php";

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');


function cooksql($request){
    /**
     * подготовка sql запроса из request
     */
    $sqlarr=array();
    if ($request["price"] =="cost_wholesale" || $request["price"] == "cost_rub"){
        $sql = "SELECT * FROM price_product WHERE ".$request["price"]." ";
        if ($request["min_price"] == ""){
            $sql= $sql." BETWEEN (SELECT MIN(".$request["price"].") FROM price_product) ";
        }else{
            $sqlarr[]=(float)$request["min_price"];
            $sql=$sql."BETWEEN ? ";
        }
        if($request["max_price"]==""){
            $sql=$sql."AND (SELECT MAX(".$request["price"].") FROM price_product) ";
        }
        else{
            $sqlarr[]=(float)$request["max_price"];
            $sql=$sql."AND ? ";
        }
    }
    if($request["number_quantity"]!=""){
        $sqlarr[]=(int)$request["number_quantity"];
        if ($request["quantity"]==">"){
            $sql = $sql."AND (quantity_stock_1 + quantity_stock_2) > ?";
        }else if($request["quantity"]=="<"){
            $sql = $sql."AND (quantity_stock_1 + quantity_stock_2) < ?";
        }
    }
        $sql = $sql.";";
    return [$sql, $sqlarr];
}

function getdbData($request){
    /**
     * получение данных по запоросу request
     */
    $temp=cooksql($request);
    try{
        $db = new DB("price","root",
            "mysqlpass", "mysql",
            "localhost", "utf8");
        $result = $db->run(...$temp)->fetchAll();
        return $result;
    }
    catch (PDOException $e){
        echo "fail ".$e->getMessage();
    }
}

function checkTable(){
    /**
     * проверка на присутсвие готовой таблицы
     */
    try {
        $db = new DB("price","root", "mysqlpass", "mysql", "localhost", "utf8");
        $db->run("DESCRIBE price_product");
        return true;
    }
    catch (Exception $e){
        return false;
    }
}

