<?php

    require_once 'config/Database.php';
    require_once 'model/UserModel.php';

    class UserController {

        private $db;
        private $model;
        public $isConnected = false;
        public $user_id;
        public $username;
        public $is_admin = false;

        public function __construct() {
            $this->db = (new Database())->getConnection();
            $this->model = new UserModel($this->db);

            if(isset($_SESSION['username'])) {
                $this->model->load_profile($_SESSION['username']);
                $this->username = $this->model->username;
                $this->user_id = $this->model->user_id;
                $this->is_admin = $this->model->is_admin;
                $this->isConnected = true;
            }
        }

        public function login($username, $password) {
            $loginData = [];

            // Incorrect login
            $loginResult = $this->model->login($username, $password);
            if($loginResult['success'] == false) {
                $loginData['status'] = 1;
                $loginData['redirectUrl'] = "index.php?error=incorrectpass";
                return $loginData;
            }

            // Correct login
            $loginData['status'] = 0;
            $loginData['redirectUrl'] = "index.php?view=home";
            $_SESSION['username'] = $username;
            return $loginData;
        }

        public function logout() {
            session_destroy();
            $data['status'] = 0;
            $data['redirectUrl'] = "index.php" ;
            return $data;
        }

        public function register($username, $email, $password, $privileges) {
            $r = $this->model->register($username, $email, $password, $privileges);
            $data = [];

            if($r === "username_taken") {    
                $data['status'] = 1;
                $data['redirectUrl'] = 'index.php?view=register&error=username_taken';
                return $data;
            }

            if ($r === "not_valid_broh" || $r == false ) {
                $data['status'] = 1;
                $data['redirectUrl'] = 'index.php?view=register&error=not_valid_broh';
                return $data;
            }

            // Example of mail sending when the user registers
            // $this->sendRegistrationMail($username)

            $data['status'] = 0;
            $data['redirectUrl'] = 'index.php?msg=register_success';
            return $data;
        }

        // Example of mail sending when the user registers
        private function sendRegistrationMail ($username) {
            $to = $this->model->getData($username, 'email');
            $from = "admin@seas-vm.test";
            $subject = "Registration succesfully";
            $message = "Welcome @".$username." to our site. \n\n Thanks for your registration.";
            $headers = "From:" . $from;
            if (mail($to, $subject, $message, $headers)) {
                return true;
            }
            return false;
        }

        public function is_admin($username) {
            return $this->model->is_admin($username);
        }
    }

?>