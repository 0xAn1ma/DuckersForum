<?php

    class ForumModel {

        private $conn;

        public $title;
        public $description;
        public $user_id;

        public function __construct($db) {
            $this->conn = $db;
        }

        function does_section_exist($title) {
            $query = "SELECT title FROM sections WHERE title=:title";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":title", $title);
            $stmt->execute();

            // Check if any rows are returned
            if($stmt->rowCount() > 0) {
                // section name exists in DB
                return true;
            }
            // section name does not exist
            return false;
        }

        function create_section($title, $description, $user_id) {
            if($this->does_section_exist($title) == true) {
                return "section_name_taken";
            }
            
            echo $user_id;
            $query = "INSERT INTO sections SET title=:title, description=:description, user_id=:user_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":user_id", $user_id);

            if($stmt->execute()) {
                return true;
            }
            return false;
        
        }

        // Obtener data de todas las secciones
        public function get_sections() {
            $query = "SELECT * FROM sections";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }

        public function getData($username, $column) {

            // 1. Comprobar que el usuario existe
            if($this->is_registered($username) == false) {
                return "not_registered";
            } 

            // 2. Comprobar que la columna existe
            $query = "SHOW COLUMNS FROM users WHERE Field=:column";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":column", $column);
            $stmt->execute();

            if($stmt->rowCount() == 0) {
                return "column_not_found";
            }

            // 4. Obtener datos con la query
            $query = "SELECT :column FROM users WHERE username=:username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":column", $column);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            // 5. Devolver los datos
            $columnData = $stmt->fetch(PDO::FETCH_NUM);
            return $columnData[0];

        }
        
    }
 
?>