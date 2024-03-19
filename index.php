<?php
    session_start();
    const INIT = "1314";

    //  ____       _                 
    // |  _ \  ___| |__  _   _  __ _ 
    // | | | |/ _ \ '_ \| | | |/ _` |
    // | |_| |  __/ |_) | |_| | (_| |
    // |____/ \___|_.__/ \__,_|\__, |
    //                         |___/ 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    //  ____                            _                 _           
    // |  _ \  ___ _ __   ___ _ __   __| | ___ _ __   ___(_) ___  ___ 
    // | | | |/ _ \ '_ \ / _ \ '_ \ / _` |/ _ \ '_ \ / __| |/ _ \/ __|
    // | |_| |  __/ |_) |  __/ | | | (_| |  __/ | | | (__| |  __/\__ \
    // |____/ \___| .__/ \___|_| |_|\__,_|\___|_| |_|\___|_|\___||___/
    //            |_|                                                 

    require_once 'config/Database.php';
    require_once 'controller/UserController.php';
    require_once 'controller/ForumController.php';

    $userController = new UserController();
    $forumController = new ForumController($userController);

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
        // SESION
        if ($_GET['action'] === 'login') {
            $data = $userController->login($_POST['username'], $_POST['password']);
            header("Location: " .$data['redirectUrl']);
            exit();
        }
        if ($_GET['action'] === "logout") {
            $data = $userController->logout();
            header("Location: " .$data['redirectUrl']);
            exit();
        }
        if ($_GET['action'] === 'register') {
            // Comprueba si el usuario está conectado
            if($userController->get_is_connected()) {
                header("Location: index.php");
                exit();
            }
            // Comprueba si alguno de los datos están vacíos o si no existen
            if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['password']) || empty($_POST['password'])|| !isset($_POST['email']) || empty($_POST['email'])) {
                header("Location: index.php?view=register&error=not_valid_broh");
                exit();
            }
            // Establece el valor de los privilegios en 'user' por defecto
            $privileges = 'user';
            $data = $userController->register($_POST['username'], $_POST['email'], $_POST['password'], $privileges);
            header("Location: " .$data['redirectUrl']);
            exit();
        }

        // SECTION
        if ($_GET['action'] === "create_section") {
            // Comprueba si el usuario está conectado
            if(!$userController->get_is_connected()) {
                //var_dump($userController->get_is_connected());
                header("Location: index.php?view=home&error=user_not_connected");
                exit();
            }
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $forumController->create_section($requestData->title, $requestData->description);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
        if ($_GET['action'] === "edit_section") {
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $forumController->edit_section($requestData->id, $requestData->title, $requestData->description);
            //header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
        if ($_GET['action'] === "delete_section") {
            $data = $forumController->delete_section($_GET['section_id']);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }

        // THREAD
        if ($_GET['action'] === "create_thread") {
            if(!$userController->get_is_connected()) {
                header("Location: index.php?view=home&error=user_not_connected");
                exit();
            }
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $forumController->create_thread($requestData->title, $requestData->msg, $_GET['section'], $userController->user_id);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
        if ($_GET['action'] === "edit_thread") {
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $forumController->edit_thread($requestData->id, $requestData->title, $requestData->msg);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
        if ($_GET['action'] === "delete_thread") {
            $thread_id = $_GET['id'];
            $data = $forumController->delete_thread($thread_id);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }

        // POST
        if ($_GET['action'] === "create_post") {
            if(!$userController->get_is_connected()) {
                header("Location: index.php?view=home&error=user_not_connected");
                exit();
            }
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $forumController->create_post($_GET['section'], $_GET['thread'], $userController->get_user_id(), $requestData->msg);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
        if ($_GET['action'] === "delete_post") {
            $post_id = $_GET['id'];
            $data = $forumController->delete_post($post_id);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
        if ($_GET['action'] === "edit_post") {
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $forumController->edit_post($requestData->id, $requestData->msg);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }

        // PROFILE
        if ($_GET['action'] === "edit_password") {
            if(!$userController->get_is_connected()) {
                header("Location: index.php?view=home&error=user_not_connected");
                exit();
            }
            $id = $userController->get_user_id();
            $rawPostData = file_get_contents("php://input");
            $requestData = json_decode($rawPostData);
            $data = $userController->edit_password($id, $requestData->password, $requestData->currentpass);
            header('Content-Type: application/json');
            echo(json_encode($data));
            exit();
        }
    }
  
    // __     ___                  ____            _             _ _           
    // \ \   / (_) _____      __  / ___|___  _ __ | |_ _ __ ___ | | | ___ _ __ 
    //  \ \ / /| |/ _ \ \ /\ / / | |   / _ \| '_ \| __| '__/ _ \| | |/ _ \ '__|
    //   \ V / | |  __/\ V  V /  | |__| (_) | | | | |_| | | (_) | | |  __/ |   
    //    \_/  |_|\___| \_/\_/    \____\___/|_| |_|\__|_|  \___/|_|_|\___|_|   
                                                                            

    if (isset($_GET['view'])) {
        // HOME
        if ($_GET['view'] === 'home') {
            $sections = $forumController->get_sections();
            $view = "view/home.php";
            include 'view/template.php';
            exit();
        }

        // REGISTER
        if ($_GET['view'] === 'register') {
            if($userController->get_is_connected()) {
                header("Location: index.php");
                exit();
            }
            $view = 'view/registerform.php';
            include 'view/template.php';
            exit();
        }

        // LOGIN
        if ($_GET['view'] === 'login') {
            if($userController->get_is_connected()) {
                header("Location: index.php");
                exit();
            }
            $view = "view/login.php";
            include 'view/template.php';
            exit();
        }

        // THREADS
        if ($_GET['view'] === 'threads') {
            if(!isset($_GET['section'])) {
                header("Location: index.php");
                exit();
            }
            $section_id = $_GET['section'];
            $section = $forumController->get_section_data($section_id);
            if(isset($section['status']) && $section['status'] === 1) {
                header("Location: ".$section['redirectUrl']);
                exit();
            }
            $threads = $forumController->get_threads_section($section_id);
            $view = "view/threads.php";
            include 'view/template.php';
            exit();
        }

        // POSTS
        if ($_GET['view'] === 'posts') {
            $thread_id = $_GET['thread'];
            $thread = $forumController->get_thread($thread_id);
            if(isset($thread['status']) && $thread['status'] === 1) {
                header('Location: '.$thread['redirectUrl']);
                exit();
            }
            $sections = $forumController->get_sections();
            $section = $sections[0];
            $posts = $forumController->get_posts_thread($thread_id);
            $view = "view/posts.php";
            include 'view/template.php';
            exit();
        }

        // PROFILE
        if($_GET['view'] === 'profile') {
            if(!$userController->get_is_connected()) {
                header("Location: index.php");
                exit();
            }
            $user_id = $userController->get_user_id();
            $user_threads = $userController->count_threads($user_id);
            $user_posts = $userController->count_posts($user_id);
            $view = "view/profile.php";
            include 'view/template.php';
            exit();
        }

        // MY THREADS
        if($_GET['view'] === 'mythreads') {
            if(!$userController->get_is_connected()) {
                header("Location: index.php");
                exit();
            }
            $sections = $forumController->get_sections();
            $section = $sections[0];
            $user_id = $userController->get_user_id();
            $threads = $userController->get_my_threads($user_id);
            $thread = $threads[0];
            $view = "view/mythreads.php";
            include 'view/template.php';
            exit();
        }

        // MY POSTS
        if($_GET['view'] === 'myposts') {
            if(!$userController->get_is_connected()) {
                header("Location: index.php");
                exit();
            }
            $user_id = $userController->get_user_id();
            $posts = $userController->get_my_posts($user_id);
            $post = $posts[0];
            // var_dump($post);
            // exit();
            $view = "view/myposts.php";
            include 'view/template.php';
            exit();
        }
    }

?>
