<?php

error_reporting(0);
class Db
{

    private $databaseName;
    private $host;
    private $user;
    private $password;
    private $db;

    /**
     * pdo_db constructor.
     */
    function __construct()
    {
        /*
        $this->databaseName = 'mycodebu_aweber';
        $this->host = 'localhost';
        $this->user = 'mycodebu_aweber';
        $this->password = 'aK6SKymc';
        $dsn = "mysql:dbname=$this->databaseName;host=$this->host";
        try {
            $db = new PDO($dsn, $this->user, $this->password);
            $this->db = $db;
        } // end try
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        */


        $this->databaseName = 'aweber';
        $this->host = 'mysql.theadriangee.com';
        $this->user = 'awever';
        $this->password = 'aK6SKymc*';
        $dsn = "mysql:dbname=$this->databaseName;host=$this->host";
        try {
            $db = new PDO($dsn, $this->user, $this->password);
            $this->db = $db;
        } // end try
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * @param $query
     * @return int
     */
    public function numrows($query)
    {
        //echo "Query: ".$query."<br>";
        $result = $this->db->query($query);
        return $result->rowCount();
    }

    /**
     * @param $query
     * @return PDOStatement
     */
    public function query($query)
    {
        //echo "Query: ".$query."<br>";
        return $this->db->query($query);
    }

}