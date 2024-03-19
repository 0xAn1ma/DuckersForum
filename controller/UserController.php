<?php
    // Dependencies
    require_once 'config/Database.php';
    require_once 'model/UserModel.php';

    //  _   _ ____  _____ ____     ____ ___  _   _ _____ ____   ___  _     _     _____ ____  
    // | | | / ___|| ____|  _ \   / ___/ _ \| \ | |_   _|  _ \ / _ \| |   | |   | ____|  _ \ 
    // | | | \___ \|  _| | |_) | | |  | | | |  \| | | | | |_) | | | | |   | |   |  _| | |_) |
    // | |_| |___) | |___|  _ <  | |__| |_| | |\  | | | |  _ <| |_| | |___| |___| |___|  _ < 
    //  \___/|____/|_____|_| \_\  \____\___/|_| \_| |_| |_| \_\\___/|_____|_____|_____|_| \_\
                                                                                          
    class UserController {

        private $db;
        private $model;
        private $is_connected = false;
        public $user_id;
        public $username;
        public $is_admin = false;
        private $total_threads;
        private $total_posts;
        private $registration_date;

        public function __construct() {
            $this->db = (new Database())->getConnection();
            $this->model = new UserModel($this->db);

            if(isset($_SESSION['username'])) {
                $this->model->load_profile($_SESSION['username']);
                $this->username = $this->model->username;
                $this->user_id = $this->model->user_id;
                $this->is_admin = $this->model->is_admin;
                $this->is_connected = true;
                $this->total_threads = $this->model->total_threads;
                $this->total_posts = $this->model->total_posts;
                $this->registration_date = $this->model->registration_date;
            }
        }

        //  ____        _           ____ _               _    _             
        // |  _ \  __ _| |_ __ _   / ___| |__   ___  ___| | _(_)_ __   __ _ 
        // | | | |/ _` | __/ _` | | |   | '_ \ / _ \/ __| |/ / | '_ \ / _` |
        // | |_| | (_| | || (_| | | |___| | | |  __/ (__|   <| | | | | (_| |
        // |____/ \__,_|\__\__,_|  \____|_| |_|\___|\___|_|\_\_|_| |_|\__, |
        //                                                            |___/ 

        // Comprueba si el usuario es administrador
        public function is_admin($username) {
            return $this->model->is_admin($username);
        }
        // Comprueba si el usuario está registrado
        public function is_registered($username) {
            return $this->model->is_registered($username);
        }
        
        //      _        _   _                 
        //     / \   ___| |_(_) ___  _ __  ___ 
        //    / _ \ / __| __| |/ _ \| '_ \/ __|
        //   / ___ \ (__| |_| | (_) | | | \__ \
        //  /_/   \_\___|\__|_|\___/|_| |_|___/
        
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

        public function edit_password($id, $password, $currentpass) {
            
            $data = [];
             // Comprueba si el usuario esta logeado y concide con el usuario a editar
            if ($this->get_is_connected() == false || $this->get_user_id() !== $id) {
                $data['status'] = 1;
                $data['msg'] = 'error';
                $data['error'] = 'not_correct_user';
                return $data;
            }

            // Comprueba si alguno de los datos se encuentra vacío
            if (empty($id) || empty($password)) {
                $data['status'] = 1;
                $data['msg'] = 'error';
                $data['error'] = 'empty_data';
                return $data;
            }

            // Comprueba si la contraseña actual es correcta antes de actualizarla
            if($this->model->is_correct_password($id, $currentpass) === false) {
                
                exit();
                $data['status'] = 1;
                $data['msg'] = 'error';
                $data['error'] = 'incorrect_pass';
                return $data;
            }

            // // Manda la orden al modelo para que se edite la sección
            $r = $this->model->edit_password($id, $password);

            // Si la respuesta es negativa
            if ($r === false) {
                $data['status'] = 1;
                $data['msg'] = 'error';
                $data['error'] = 'edit_failed';
                return $data;
            }

            // Si la respuesta es satisfactoria
            $data['status'] = 0;
            $data['msg'] = 'ok';
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

        //      ____      _     ____        _        
        //     / ___| ___| |_  |  _ \  __ _| |_ __ _ 
        //    | |  _ / _ \ __| | | | |/ _` | __/ _` |
        //    | |_| |  __/ |_  | |_| | (_| | || (_| |
        //     \____|\___|\__| |____/ \__,_|\__\__,_|


        public function get_is_connected() {
            return $this->is_connected;
        }

        public function get_user_id() {
            return $this->user_id;
        }

        public function get_registration_date() {
            return $this->registration_date;
        }

        public function count_threads($user_id) {
            return $this->model->count_threads($user_id);
        }

        public function get_my_threads($user_id) {
            return $this->model->get_my_threads($user_id);
        }

        public function count_posts($user_id) {
            return $this->model->count_posts($user_id);
        }
        
        public function get_my_posts($user_id) {
            return $this->model->get_my_posts($user_id);
        }
    }

?>