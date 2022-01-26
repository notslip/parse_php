<?php

require_once "db.php";

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');


function cooksql($request){
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
    $temp=cooksql($request);
    try{
        $conn = connect_db();

        $stmt = $conn->prepare($temp[0]);
        for($i=1, $s=count($temp[1]); $i<=$s; $i++){
            $stmt->bindParam($i,$temp[1][$i-1], PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result == false){
            return $stmt->errorInfo();
        }
        else{
            return $result;
        }
    }
    catch (PDOException $e){
        echo "fail ".$e->getMessage();
    }
}

