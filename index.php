<?php
    session_start();
    const INIT = "1314";

    // Debug
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Dependencies
    require_once 'config/Database.php';
    require_once 'controller/UserController.php';
    require_once 'controller/ForumController.php';


    $userController = new UserController();
    $forumController = new ForumController($userController);

    // Default page
    if(!isset($_GET['action']) && !isset($_GET['view'])) {
        if($userController->isConnected == true) {
            header("Location: index.php?view=home");
            exit();
        }
        include 'view/login.php';
        exit();
    }

    // Security Check
    if (isset($_GET['action']) && isset($_GET['view'])) {
        echo "Security flagged.";
        exit();
    }

    // Action Controller
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'login') {
            $data = $userController->login($_POST['username'], $_POST['password']);
            header("Location: " .$data['redirectUrl']);
            exit();
        }

        if($_GET['action'] === "logout") {
            $data = $userController->logout();
            header("Location: " .$data['redirectUrl']);
            exit();
        }

        if($_GET['action'] === 'register') {
            if($userController->isConnected == true) {
                header("Location: index.php");
                exit();
            }
            if(!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['password']) || empty($_POST['password'])|| !isset($_POST['email']) || empty($_POST['email'])) {
                header("Location: index.php?view=register&error=not_valid_broh");
                exit();
            }

            $privileges = 'user';
            $data = $userController->register($_POST['username'], $_POST['email'], $_POST['password'], $privileges);
            header("Location: " .$data['redirectUrl']);
            exit();
        }

        if($_GET['action'] === "create_section") {
            if($userController->isConnected == false) {
                header("Location: index.php?view=home&error=user_not_connected");
                exit();
            }
            $data = $forumController->create_section($_POST['title'], $_POST['description']);
            header("Location: " .$data['redirectUrl']);
            exit();
        }
    }
    
    // View Controller
    if (isset($_GET['view'])) {
        if ($_GET['view'] === 'home') {
            if($userController->isConnected == false) {
                header("Location: index.php");
                exit();
            }
            $sections = $forumController->get_sections();
            include 'view/home.php';
            exit();
        }

        if ($_GET['view'] === 'register') {
            if($userController->isConnected == true) {
                header("Location: index.php");
                exit();
            }
            include 'view/registerform.php';
            exit();
        }

        if ($_GET['view'] === 'login') {
            if($userController->isConnected == true) {
                header("Location: index.php");
                exit();
            }
            include 'view/login.php';
            exit();
        }




    }

  

?>