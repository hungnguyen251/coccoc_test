<?php

class DB
{
    protected $db;
    protected $dbHost = "127.0.0.1";
    protected $dbName = "coccoc_test";
    protected $dbUser = "root";
    protected $dbPass = "";
    protected $tableName;

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName",$this->dbUser, $this->dbPass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (Exception $exception) {
            echo "Connection failed";
            echo $exception->getMessage();
            $this->db = null;
        }
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }
}

?>