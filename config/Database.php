<?php

    class Database {

        // Definimos las variables para la DB.
        private $host = 'localhost';
        private $user = 'admin';
        private $pass = 'admin';
        private $name = 'infuria';
        public  $conn;

        function getConnection() {
            $this->conn = null;
            
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->name, $this->user, $this->pass);
            }
            catch(PDOException $exception) {
                echo "Connection error: ".$exception->getMessage();
            }

            return $this->conn;
        }

    }

?>






