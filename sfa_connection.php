<?php
ob_clean(); 
ob_start();

define('SAATHI_URL','https://starsaathi.com');
class sfa_connection {
    public $conn;
    public $host;
    public $user;
    public $password;
    public $database;

    public function __construct() {
        $this->host = "localhost";
        $this->user = "root";
        $this->password = "Passw0rd123#$";
        $this->database = "acedns_STAR";

        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
}
