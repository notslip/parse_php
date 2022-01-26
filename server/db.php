<?php



function connect_db($dbname = "price"):PDO
{
    $dsn = "mysql:host=localhost;charset=utf8;dbname=".$dbname;
    $username = "root";
    $password = "mysqlpass";

    try {
        // подключаемся к серверу
        $db = new PDO(
            $dsn,
            $username,
            $password,
        );
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $db;

    }
    catch (PDOException $e) {
        echo "Connection failed: " . $e->getTraceAsString();
    }
}

