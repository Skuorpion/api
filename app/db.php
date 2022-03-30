<?php
/**
 * Connect MySQL with PDO class
 */
class db {

    private $dbhost = '127.0.0.1:3307';
    private $dbuser = 'root';
    private $dbpass = 'root';
    private $dbname = 'api';

    public function connect() {

        $dbConn = new PDO(
            "mysql:host=$this->dbhost;dbname=$this->dbname",
            $this->dbuser,
        //$this->dbpass
        );

        $dbConn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


        return $dbConn;
    }
}