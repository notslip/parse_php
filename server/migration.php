<?php

require_once "db.php";
require_once "parse.php";
require_once "utils.php";

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

function create_db($name){
    try {
        // подключаемся к серверу
        $db = new PDO(
            'mysql:host=localhost;charset=utf8',
            "root",
            "mysqlpass",
            array(PDO::ATTR_PERSISTENT => true)
        );
        $sql = "CREATE DATABASE ".$name.";";
        // выполняем SQL-выражение
        $db->exec($sql);
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}


function create_table($names_col=""){
    //забью пока запрос вручную
    //надо будет сделать автоматическое определение типов
    try{
        $conn = connect_db();
        $sql = "CREATE TABLE price_product(id INT AUTO_INCREMENT PRIMARY KEY, 
        name_product VARCHAR(100), 
        cost_rub DECIMAL(9,2),
        cost_wholesale DECIMAL(9,2),
        quantity_stock_1 INT,
        quantity_stock_2 INT,
        source_country VARCHAR(20)
        )";
        $conn->exec($sql);
    }
    catch (PDOException $e){
        echo "Connection failed: " . $e->getMessage();
    }
}

function save_data($arr){
    //готовим sql запрос
    $arr_name_col = $arr[1];
    $arr_name_col = rename_key($arr_name_col, "en");
    $sql = "INSERT INTO price_product (";
    foreach (array_keys($arr_name_col) as $key) {
        $sql = $sql . $key . ", ";
    }
    $sql = rtrim($sql, ", ") . ") VALUES (";
    foreach (array_keys($arr_name_col) as $i) {
        $sql = $sql . "?, ";
    }
    $sql = rtrim($sql, ", ") . ");";
    //подключаемся к бд
    try {
        $conn = connect_db();
        foreach ($arr as $arr_val) {
            $arr_val=array_values($arr_val);
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $arr_val[0], PDO::PARAM_STR);
            $stmt->bindParam(2, $arr_val[1], PDO::PARAM_STR);
            $stmt->bindParam(3, $arr_val[2],PDO::PARAM_STR);
            $stmt->bindParam(4, $arr_val[3],PDO::PARAM_INT);
            $stmt->bindParam(5, $arr_val[4],PDO::PARAM_INT);
            $stmt->bindParam(6, $arr_val[5],PDO::PARAM_STR);
            $rowsNumber = $stmt->execute();
            if ($rowsNumber == false){
                var_dump($stmt->errorInfo());
            }
        }
    }
    catch ( PDOException $e){
        echo "fail: ". $e->getMessage();
    }

}

function checkTable(){
    try {
        $conn = connect_db();
        $sql = "DESCRIBE price_product";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        return true;
    }
    catch (Exception $e){
        return false;
    }
}

function createData(){
    create_table();
    $parser = new Parse("pricelist.xls");
    save_data($parser->get_Data());
}
