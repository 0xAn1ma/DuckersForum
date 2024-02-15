<?php

    class UserModel {

        private $conn;

        public $user_id;
        public $username;
        public $email;
        public $privileges;
        public $registration_date;
        public $avatar;
        public $is_admin;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function sanitize($str) {
            return $str;
            // return htmlspecialchars(strip_tags($str));
        }

        public function login($username, $password) {

            if($this->is_registered($username) == false) {
                return false;
            }

            $query = "SELECT password FROM users WHERE username=:username";
            $stmt = $this->conn->prepare($query);
            $username = $this->sanitize($username);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $data = [];
                if (!password_verify($password, $row['password'])) {
                    $data['success'] = false;
                    return $data;
                }
                $data['success'] = true;
                return $data;
            }
            
        }

        public function load_profile($username) {
            $query = "SELECT * FROM users WHERE username=:username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $profile = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

            $this->user_id = $profile['id'];
            $this->username = $profile['username'];
            $this->email = $profile['email'];
            $this->privileges = $profile['privileges'];
            $this->registration_date = $profile['registration_date'];
            $this->avatar = $profile['avatar'];
            $this->is_admin =  $profile['privileges'] === 'admin' ? true : false;
        }

        private function get_profile($username) {
            $query = "SELECT * FROM users WHERE username=:username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $profile = $stmt->fetchAll(PDO::FETCH_ASSOC);
            var_dump($profile);
            exit();
        }

        public function is_admin($username) { 
            return $this->is_admin === true ? true : false;           
        }
        
        public function is_registered($username) {
            $query = "SELECT username FROM users WHERE username=:username";
            $stmt = $this->conn->prepare($query);

            // Sanitize
            $username = $this->sanitize($username);

            // Bind values
            $stmt->bindParam(":username", $username);

            // Execute the query
            $stmt->execute();

            // Check if any rows are returned
            if($stmt->rowCount() > 0) {
                // User with the username exists
                return true;
            }
            return false;
        }


        public function register($username, $email, $password, $privileges) {
            if($this->is_registered($username) == true) {
                return "username_taken";
            }

            $registration_date = date('Y-m-d H:i:s');
            $query = "INSERT INTO users SET username=:username, email=:email, password=:password, privileges=:privileges, registration_date=:registration_date";
            $stmt = $this->conn->prepare($query);

            // sanitize
            // Check that the email syntax is correct
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "not_valid_broh";
            }
            $username = $this->sanitize($username);
            $email = $this->sanitize($email);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $privileges = $this->sanitize($privileges);

            // bind values
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":privileges", $privileges);
            $stmt->bindParam(":registration_date", $registration_date);


            if($stmt->execute()) {
                return true;
            }
            return false;
        
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