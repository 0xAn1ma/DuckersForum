<?php

require_once 'config/Database.php';
require_once 'model/ForumModel.php';

class ForumController {
    
    private $db;
    public $userController;
    private $model;

    public function __construct($userController) {
        $this->db = (new Database())->getConnection();
        $this->userController = $userController;
        $this->model = new ForumModel($this->db);
    }

    public function create_section($title, $description) {
        if($this->userController->is_admin($this->userController->username) == false) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=is_not_an_admin';
            return $data;
        }
        
        $r = $this->model->create_section($title, $description, $this->userController->user_id);
        $data = [];

        if($r === "section_name_taken") {    
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=section_name_taken';
            return $data;
        }

        $data['status'] = 0;
        $data['redirectUrl'] = 'index.php?msg=section_created_success';
        return $data;
    }

    // Obtener data de todas las secciones
    public function get_sections() {
        return $this->model->get_sections();
    }

    // Obtener data para 1 seccion
    public function get_section_data() {
   
    }     

    // Crear secciones

    // THREADS

    // ANSWERS
}

?>