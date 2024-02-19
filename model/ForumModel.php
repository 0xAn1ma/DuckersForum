<?php

    class ForumModel {

        private $conn;
        public $user_id;
        
        public function __construct($db) {
            $this->conn = $db;
        }

        function does_section_exist($section_id) {
            $query = "SELECT id FROM sections WHERE id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);
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

        function delete_section($section_id) {
            if($this->does_section_exist($section_id) == false) {
                return "section_id_does_not_exist";
            }

            $query = "DELETE FROM sections WHERE id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);

            if($stmt->execute()) {
                return true;
            }
            return false;
        }

        function edit_section($id, $title, $description){
            if($this->does_section_exist($id) == false) {
                return "section_id_does_not_exist";
            }

            $query = "UPDATE sections SET title=:title, description=:description WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":id", $id);
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

        public function get_section_data($section_id) {
            $query = "SELECT * FROM sections WHERE id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);
            $stmt->execute();
            $section = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            return $section;
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