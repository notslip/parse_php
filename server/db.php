<?php

class DB extends PDO {
    /**
     * wrapper over PDO set atributes and castomise call connection
     */
        function __construct(string $dbname, string $username, string $password, string $driver="mysql", string $host="localhost",string $charset="utf8")
        {
            $dsn=$driver.":host=".$host.";charset=".$charset.";dbname=".$dbname;
            parent::__construct($dsn, $username, $password);
            $this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
        }
        public function run(string $sql, array $args=[]): PDOStatement
    /**
     *combined preparation and execution of a query with an optional array for value substitution
     */
    {
        if (!$args)
        {
            return $this->query($sql);
        }
        $stmt = $this->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}

