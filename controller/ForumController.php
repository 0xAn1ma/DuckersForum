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

    public function delete_section($section_id) {
        $data = [];
        if($this->userController->is_admin($this->userController->username) == false) {
            $data['status'] = 1;
            $data['error'] = 'is_not_an_admin';
            return $data;
        }

        $r = $this->model->delete_section($section_id);
        if($r === 'section_id_does_not_exist') {
            $data['status'] = 1;
            $data['error'] = 'section_id_does_not_exist';
            return $data;
        }

        $data['status'] = 0;
        $data['error'] = '';
        return $data;
    }

    public function edit_section($id, $title, $description) {
        $data = [];
        if ($this->userController->is_admin == false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'is_not_an_admin';
            return $data;
        }
        
        if (empty($id) || empty($title) || empty($description)) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'empty_data';
            return $data;
        }

        $r = $this->model->edit_section($id, $title, $description);

        if ($r === false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'edit_failed';
            return $data;
        }

        $data['status'] = 0;
        $data['msg'] = 'ok';
        return $data;
    }

    // Obtener data de todas las secciones
    public function get_sections() {
        return $this->model->get_sections();
    }   

    // Crear secciones

    // THREADS

    // ANSWERS
}

?>