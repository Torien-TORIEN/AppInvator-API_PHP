<?php
//dans database/config.db.php
class Database {
    // Properties
    private $host = "localhost";
    private $dbname = "torientorien5901";
    private $username = "torientorien5901";
    private $password = "0Eq17Pbk8lOv";

    // Methods
    public function connect_to_db() {
        $conn = null;
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
        } catch (\PDOException $e) {
            echo "Connection to database error: " . $e->getMessage();
        }
        //echo "Connexion successfull !";
        return $conn;
    }
}