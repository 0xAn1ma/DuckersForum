<?php
    
    class Database {

        // Definimos las variables para la DB.
        public  $conn;
        private $dbhost;
        private $dbname;
        private $dbuser;
        private $dbpass;

        public function __construct($dbhost, $dbname, $dbuser, $dbpass) {
            $this->dbhost = $dbhost;
            $this->dbname = $dbname;
            $this->dbuser = $dbuser;
            $this->dbpass = $dbpass;
        }

        function getConnection() {
            $this->conn = null;
            
            try {
                $this->conn = new PDO("mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname, $this->dbuser, $this->dbpass);
            }
            catch(PDOException $exception) {
                echo "Connection error: ".$exception->getMessage();
            }

            return $this->conn;
        }

    }

?>






