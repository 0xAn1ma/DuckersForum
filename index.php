<?php
    //  ____       _                 
    // |  _ \  ___| |__  _   _  __ _ 
    // | | | |/ _ \ '_ \| | | |/ _` |
    // | |_| |  __/ |_) | |_| | (_| |
    // |____/ \___|_.__/ \__,_|\__, |
    //                         |___/ 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $debug = false;

    //  ____                            _                 _           
    // |  _ \  ___ _ __   ___ _ __   __| | ___ _ __   ___(_) ___  ___ 
    // | | | |/ _ \ '_ \ / _ \ '_ \ / _` |/ _ \ '_ \ / __| |/ _ \/ __|
    // | |_| |  __/ |_) |  __/ | | | (_| |  __/ | | | (__| |  __/\__ \
    // |____/ \___| .__/ \___|_| |_|\__,_|\___|_| |_|\___|_|\___||___/
    //            |_|                                                 
    session_start();
    const INIT = "1314";
    require_once 'lib/ForumUtils.php';
    require_once 'controller/DataController.php';
    
    if (!ForumUtils::isForumInstalled()) {

        if(isset($_GET['action']) && $_GET['action'] === 'install') {
            $jsonData = DataController::decodeJson();
            // var_dump($jsonData);

            // Comprobar que los campos no están vacíos
            if(empty($jsonData->dbhost) || empty($jsonData->dbname) || empty($jsonData->dbuser) || empty($jsonData->dbpass) || empty($jsonData->username) || empty($jsonData->password)){
                DataController::returnJson(DataController::generateData(1, 'empty_data', ''));
                exit();
            }

            // Conectarse a la base de datos
            try {
                $conn = new PDO("mysql:host=" . $jsonData->dbhost . ";dbname=" . $jsonData->dbname, $jsonData->dbuser, $jsonData->dbpass);
            }
            catch(PDOException $exception) {
                DataController::returnJson(DataController::generateData(1, 'mysql_connection_failed', '', [$exception->getMessage()]));
                exit();
            }

            // Create tables
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $queryArray = [
                "DROP TABLE IF EXISTS `posts`",
                "DROP TABLE IF EXISTS `sections`",
                "DROP TABLE IF EXISTS `threads`",
                "DROP TABLE IF EXISTS `users`",
                "SET NAMES utf8mb4",
                "SET FOREIGN_KEY_CHECKS = 0",
                "CREATE TABLE `users` (`id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(128) NOT NULL, `email` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `privileges` varchar(50) NOT NULL, `registration_date` datetime NOT NULL, `avatar` varchar(255) NOT NULL DEFAULT '', PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                "CREATE TABLE `sections` (`id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `description` text NOT NULL, `creation_date` datetime NOT NULL DEFAULT current_timestamp(), `user_id` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `created_by` (`user_id`), CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                "CREATE TABLE `threads` (`id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `section_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `msg` text NOT NULL, `creation_date` datetime NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`), KEY `section_id` (`section_id`), KEY `created_by` (`user_id`), CONSTRAINT `threads_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`), CONSTRAINT `threads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                "CREATE TABLE `posts` (`id` int(11) NOT NULL AUTO_INCREMENT, `section_id` int(11) NOT NULL, `thread_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `msg` text NOT NULL, `creation_date` datetime NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`), KEY `section_id` (`section_id`), KEY `thread_id` (`thread_id`), KEY `created_by` (`user_id`), CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`), CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`), CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                "SET FOREIGN_KEY_CHECKS = 1"
            ];

            try {
                foreach($queryArray as $query) {
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                }
            }
            catch(PDOException $exception) {
                DataController::returnJson(DataController::generateData(1, 'installation_error', '', [$exception->getMessage()]));
                exit();
            }

            // Crear usuario con permiso de administrador
            $registration_date = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO users SET username=:username, email=:email, password=:password, privileges='admin', registration_date=:registration_date");

            // Check that the email syntax is correct
            if (!filter_var($jsonData->email, FILTER_VALIDATE_EMAIL)) {
                return "not_valid_broh";
            }
            
            $password = password_hash($jsonData->password, PASSWORD_DEFAULT);
            $stmt->bindParam(":username", $jsonData->username);
            $stmt->bindParam(":email", $jsonData->email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":registration_date", $registration_date);
            $stmt->execute();

            // Crear fichero config.php
            $config =  "<?php\n";
            $config .= "\$dbHost = '$jsonData->dbhost';\n";
            $config .= "\$dbName = '$jsonData->dbname';\n";
            $config .= "\$dbUser = '$jsonData->dbuser';\n";
            $config .= "\$dbPass = '$jsonData->dbpass';\n";
            $config .= "?>\n";
            file_put_contents('config/config.php', $config);

            DataController::returnJson(DataController::generateData(0, 'ok', 'index.php'));
            exit();
        }
        $view = 'view/install.php';
        include 'view/template.php';
        exit();
    }

    include 'config/config.php';
    require_once 'config/Database.php';
    require_once 'controller/UserController.php';
    require_once 'controller/ForumController.php';
    
    $db = (new Database($dbHost, $dbName, $dbUser, $dbPass))->getConnection();
    $userController = new UserController($db);
    $forumController = new ForumController($db, $userController);
    $avatarSrc = $userController->get_avatar() === 'default' ? 'images/default-user.jpg' : 'uploads/'.$userController->get_avatar();

    //  ____        __             _ _                            
    // |  _ \  ___ / _| __ _ _   _| | |_   _ __   __ _  __ _  ___ 
    // | | | |/ _ \ |_ / _` | | | | | __| | '_ \ / _` |/ _` |/ _ \
    // | |_| |  __/  _| (_| | |_| | | |_  | |_) | (_| | (_| |  __/
    // |____/ \___|_|  \__,_|\__,_|_|\__| | .__/ \__,_|\__, |\___|
    //                                    |_|          |___/      

    if (!isset($_GET['action']) && !isset($_GET['view'])) {
        header("Location: index.php?view=home");
        exit();
    }

    //  ____                       _ _            ____ _               _    
    // / ___|  ___  ___ _   _ _ __(_) |_ _   _   / ___| |__   ___  ___| | __
    // \___ \ / _ \/ __| | | | '__| | __| | | | | |   | '_ \ / _ \/ __| |/ /
    //  ___) |  __/ (__| |_| | |  | | |_| |_| | | |___| | | |  __/ (__|   < 
    // |____/ \___|\___|\__,_|_|  |_|\__|\__, |  \____|_| |_|\___|\___|_|\_\
    //                                   |___/                              

    if (isset($_GET['action']) && isset($_GET['view'])) {
        echo "Security flagged.";
        exit();
    }

    //      _        _   _                ____            _             _ _           
    //     / \   ___| |_(_) ___  _ __    / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
    //    / _ \ / __| __| |/ _ \| '_ \  | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
    //   / ___ \ (__| |_| | (_) | | | | | |__| (_) | | | | |_| | | (_) | | |  __/ |   
    //  /_/   \_\___|\__|_|\___/|_| |_|  \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
                                                                               
    if (isset($_GET['action'])) {
        // User login
        if ($_GET['action'] === 'login') {
            $data = $userController->login($_POST['username'], $_POST['password']);
            header("Location: " .$data['redirectUrl']);
        }

        // User logout
        if ($_GET['action'] === "logout") {
            $data = $userController->logout();
            header("Location: " .$data['redirectUrl']);
        }
        
        // User registration
        if ($_GET['action'] === 'register') {
            // Requerir que el usuario no esté ya logueado
            $userController->requireNotLoggedIn();
            
            // Comprueba si alguno de los datos están vacíos o si no existen
            if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['password']) || empty($_POST['password'])|| !isset($_POST['email']) || empty($_POST['email'])) {
                header("Location: index.php?view=register&error=empty_data");
                exit();
            }

            // Registra al usuario
            $data = $userController->register($_POST['username'], $_POST['email'], $_POST['password']);
            header("Location: " .$data['redirectUrl']);
        }

        // Create section
        if ($_GET['action'] === "create_section") {
            $jsonData = DataController::decodeJson();
            DataController::returnJson($forumController->create_section($jsonData->title, $jsonData->description));
        }

        // Edit section
        if ($_GET['action'] === "edit_section") {
            $jsonData = DataController::decodeJson();
            DataController::returnJson($forumController->edit_section($jsonData->id, $jsonData->title, $jsonData->description));
        }

        // Delete section
        if ($_GET['action'] === "delete_section") {
            DataController::returnJson($forumController->delete_section($_GET['section_id']));
        }

        // Create thread
        if ($_GET['action'] === "create_thread") {
            $userController->loginRequired();
            $jsonData = DataController::decodeJson();
            DataController::returnJson($forumController->create_thread($jsonData->title, $jsonData->msg, $_GET['section'], $userController->user_id));
        }

        // Edit thread
        if ($_GET['action'] === "edit_thread") {
            $jsonData = DataController::decodeJson();
            DataController::returnJson($forumController->edit_thread($jsonData->id, $jsonData->title, $jsonData->msg));
        }

        // Delete thread
        if ($_GET['action'] === "delete_thread") {
            DataController::returnJson($forumController->delete_thread($_GET['id']));
        }

        // Create post
        if ($_GET['action'] === "create_post") {
            $jsonData = DataController::decodeJson();
            DataController::returnJson($forumController->create_post($_GET['section'], $_GET['thread'], $userController->get_user_id(), $jsonData->msg));
        }

        // Delete post
        if ($_GET['action'] === "delete_post") {
            DataController::returnJson($forumController->delete_post($_GET['id']));
        }

        // Edit post
        if ($_GET['action'] === "edit_post") {
            $jsonData = DataController::decodeJson();
            DataController::returnJson($forumController->edit_post($jsonData->id, $jsonData->msg));
        }

        // Edit password
        if ($_GET['action'] === "edit_password") {
            $jsonData = DataController::decodeJson();
            DataController::returnJson($userController->edit_password($jsonData->password, $jsonData->currentpass));
        }

        // Upload avatar
        if ($_GET['action'] === "avatar_upload") {
            try {
                if (!isset($_FILES['avatar']) || !isset($_FILES['avatar']['name']) || !isset($_FILES['avatar']['tmp_name']) || $_FILES['avatar']['tmp_name'] === "") {
                    DataController::returnJson(DataController::generateData(1, "max_size_allowed", ""));
                    exit();
                }
                $origFilename = basename($_FILES['avatar']['name']);
                $ext = pathinfo($origFilename, PATHINFO_EXTENSION);
    
                // Comprobar que la extensión es permitida
                $allowed_file_extensions = array('jpg', 'png', 'gif');
                if (!in_array($ext, $allowed_file_extensions)) {
                    DataController::returnJson(DataController::generateData(1, "bad_filetype", ""));
                    exit();
                }
    
                // Comprobar que el MIME Type es permitido
                $allowed_mime_types = array('image/jpeg','image/png','image/gif');
                $finfo = new finfo();
                $mimeType = $finfo->file($_FILES['avatar']['tmp_name'], FILEINFO_MIME_TYPE);
                if(!in_array($mimeType, $allowed_mime_types)){
                    DataController::returnJson(DataController::generateData(1, "bad_filetype", ""));
                    exit();
                }
    
                // Mover el fichero
                $filename = md5($origFilename).".".$ext;
                if (empty($filename)) {
                    DataController::returnJson(DataController::generateData(1, "max_allowed_size", ""));
                    exit();
                }
                if (!move_uploaded_file($_FILES['avatar']['tmp_name'], "./uploads/$filename")) {
                    DataController::returnJson(DataController::generateData(1, "error", ""));
                }
                $userController->save_avatar($filename);
                DataController::returnJson(DataController::generateData(0, "ok", "", [
                    "filename" => $filename
                ]));
            }
            catch (Exception $e) {
                DataController::returnJson(DataController::generateData(1, "max_size_allowed", ""));
                exit();
            }
        }

        // Delete account
        if ($_GET['action'] === "delete_account") {
            
            $jsonData = DataController::decodeJson();
            DataController::returnJson($userController->delete_account($jsonData->password));
        }
        
        exit();
    }
  
    // __     ___                  ____            _             _ _           
    // \ \   / (_) _____      __  / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
    //  \ \ / /| |/ _ \ \ /\ / / | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
    //   \ V / | |  __/\ V  V /  | |__| (_) | | | | |_| | | (_) | | |  __/ |   
    //    \_/  |_|\___| \_/\_/    \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   

    if (isset($_GET['view'])) {
        // HOME
        if ($_GET['view'] === 'home') {
            $data = $forumController->get_sections()['data'];
            $view = "view/home.php";
        }

        // REGISTER
        if ($_GET['view'] === 'register') {
            $userController->requireNotLoggedIn();
            $view = 'view/register.php';
        }

        // LOGIN
        if ($_GET['view'] === 'login') {
            $userController->requireNotLoggedIn();
            $view = "view/login.php";
        }

        // THREADS
        if ($_GET['view'] === 'threads') {
            $response = $forumController->get_section_threads($_GET['section']);
            if($response['status'] === 1) {
                $forumController->redirectToHome();
            }
            $data = $response['data'];
            $view = "view/threads.php";
        }

        // POSTS
        if ($_GET['view'] === 'posts') {
            $response = $forumController->get_thread($_GET['section'], $_GET['thread'], !isset($_GET['page']) ? 1 : $_GET['page']);
            if($response['status'] === 1) {
                $forumController->redirectToHome();
            }
            $data = $response['data'];
            $view = "view/posts.php";
        }

        // PROFILE
        if($_GET['view'] === 'profile') {
            $userController->loginRequired();
            $view = "view/profile.php";
        }

        // MY THREADS
        if($_GET['view'] === 'mythreads') {
            $userController->loginRequired();
            $data = $userController->get_my_threads()['data'];
            $view = "view/mythreads.php";
        }

        // MY POSTS
        if($_GET['view'] === 'myposts') {
            $userController->loginRequired();
            $data = $userController->get_my_posts()['data'];
            $view = "view/myposts.php";
        }



        include 'view/template.php';
        exit();
    }

?>
