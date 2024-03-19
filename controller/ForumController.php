<?php
// Dependencies
require_once 'config/Database.php';
require_once 'model/ForumModel.php';

//  _____ ___  ____  _   _ __  __    ____ ___  _   _ _____ ____   ___  _     _     _____ ____  
// |  ___/ _ \|  _ \| | | |  \/  |  / ___/ _ \| \ | |_   _|  _ \ / _ \| |   | |   | ____|  _ \ 
// | |_ | | | | |_) | | | | |\/| | | |  | | | |  \| | | | | |_) | | | | |   | |   |  _| | |_) |
// |  _|| |_| |  _ <| |_| | |  | | | |__| |_| | |\  | | | |  _ <| |_| | |___| |___| |___|  _ < 
// |_|   \___/|_| \_\\___/|_|  |_|  \____\___/|_| \_| |_| |_| \_\\___/|_____|_____|_____|_| \_\
                                                                                            
class ForumController {
    
    private $db;
    public $userController;
    private $model;

    public function __construct($userController) {
        $this->db = (new Database())->getConnection();
        $this->userController = $userController;
        $this->model = new ForumModel($this->db);
    }

    //  ____            _   _                 
    // / ___|  ___  ___| |_(_) ___  _ __  ___ 
    // \___ \ / _ \/ __| __| |/ _ \| '_ \/ __|
    //  ___) |  __/ (__| |_| | (_) | | | \__ \
    // |____/ \___|\___|\__|_|\___/|_| |_|___/
                                           
    // Crear una sección
    public function create_section($title, $description) {
        // Comprueba si el usuario logeado es administrador
        if($this->userController->is_admin($this->userController->username) == false) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=is_not_an_admin';
            return $data;
        }
        // Comprueba que los datos no estén vacios
        if(empty($title) || empty($description)) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=empty_data';
            return $data;
        }
        // Comprueba que los datos no midan más de lo estipulado
        $trimmedDescription = strip_tags($description);
        if(strlen($title) > 100 || strlen($trimmedDescription) > 200) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=incorrect_length';
            return $data;
        }

        // Manda la orden al modelo para que se cree la sección
        $r = $this->model->create_section($title, $description, $this->userController->user_id);
        $data = [];

        // Si el nombre de la sección ya existe, redirige a home con el error correspondiente
        if($r === "section_name_taken") {    
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=section_name_taken';
            return $data;
        }
        // Si la respuesta es satisfactoria
        $data['status'] = 0;
        $data['redirectUrl'] = 'index.php?msg=section_created_success';
        return $data;
    }

    // Editar una sección
    public function edit_section($id, $title, $description) {
        $data = [];
        // Comprueba si el usuario logeado tiene permisos de administrador
        if ($this->userController->is_admin == false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'is_not_an_admin';
            return $data;
        }
        // Comprueba si alguno de los datos se encuentra vacío
        if (empty($id) || empty($title) || empty($description)) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'empty_data';
            return $data;
        }
        // Comprueba que los datos no midan más de lo estipulado
        $trimmedDescription = strip_tags($description);
        if(strlen($title) > 100 || strlen($trimmedDescription) > 200) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=incorrect_length';
            return $data;
        }

        // // Manda la orden al modelo para que se edite la sección
        $r = $this->model->edit_section($id, $title, $description);

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

    // Eliminar una sección
    public function delete_section($section_id) {
        $data = [];
        // Comprueba si el usuario logeado tiene permisos de administrador
        if($this->userController->is_admin($this->userController->username) == false) {
            $data['status'] = 1;
            $data['error'] = 'is_not_an_admin';
            return $data;
        }
        // Manda la orden al modelo para que se elimine la sección
        $r = $this->model->delete_section($section_id);
        // Si el id de la sección no existe
        if($r === 'section_id_does_not_exist') {
            $data['status'] = 1;
            $data['error'] = 'section_id_does_not_exist';
            return $data;
        }
        // Si se ha eliminado correctamente
        $data['status'] = 0;
        $data['error'] = '';
        return $data;
    }

    // Obtener la información de todas las secciones
    public function get_sections() {
        // Manda la orden al modelo para obtener el data de las secciones
        return $this->model->get_sections();
    }

    public function get_section_data($section_id) {
        if(empty($section_id) || !is_numeric($section_id)) {
            $data['status'] = 1;
            $data['redirectUrl'] = "index.php";
            $data['msg'] = "empty or not int";
            return $data;
        }

        $r = $this->model->get_section_data($section_id);
        if($r === 'section_not_exist') {
            $data['status'] = 1;
            $data['redirectUrl'] = "index.php";
            return $data;
        }

        return $this->model->get_section_data($section_id);
    }

    // Obtener información de todos los threads dentro de una sección
    public function get_threads_section($section_id) {
        // Manda la orden al modelo para obtener el data de los threads dentro de una seccion
        return $this->model->get_threads_section($section_id);
    }

    // Obtener el numero de threads dentro de una sección
    public function  count_section_threads($section_id) {
        // Manda la orden al modelo para obtener el numero de threads dentro de una sección
        return $this->model->count_section_threads($section_id);
    }

    // Obtener el número de posts dentro de una sección
    public function  count_section_posts($section_id) {
        // Manda la orden al modelo para obtener el número de posts dentro de una sección
        return $this->model->count_section_posts($section_id);
    }

    //  _____ _                        _     
    // |_   _| |__  _ __ ___  __ _  __| |___ 
    //   | | | '_ \| '__/ _ \/ _` |/ _` / __|
    //   | | | | | | | |  __/ (_| | (_| \__ \
    //   |_| |_| |_|_|  \___|\__,_|\__,_|___/
     
    // Crear un thread
    public function create_thread($title, $msg, $section_id, $user_id) {
        $data = [];
        // Comprueba si el usuario está conectado
        if(!$this->userController->get_is_connected()) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_connected';
            return $data;
        }
        // Comprueba si alguno de los datos esta vacío y lanza un error en ese caso
        if (empty($section_id) || empty($title) || empty($msg)) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'empty_data';
            return $data;
        }
        // Comprueba que los datos no midan más de lo estipulado
        $trimmedMsg = strip_tags($msg);
        if(strlen($title) > 100 || strlen($trimmedMsg) > 2000) {
            var_dump($msg);
            var_dump(strlen($msg));
            var_dump($title);
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=incorrect_length';
            return $data;
        }

        // Manda la orden al modelo para que se cree el thread
        $r = $this->model->create_thread($title, $msg, $section_id, $user_id);

        // Si la respuesta es false
        if($r === false) {
            $data['status'] = 1;
            $data['redirectUrl'] = "index.php?view=threads&section=$section_id&msg=thread_created_error";
            return $data;
        }
        // Si la respuesta es true
        $data['status'] = 0;
        $data['redirectUrl'] = "index.php?view=threads&section=$section_id&msg=thread_created_success";
        return $data;
    }
    
    // Editar un thread
    public function edit_thread($id, $title, $msg) {
        
        // Comprueba si el usuario está conectado
        if(!$this->userController->get_is_connected()) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_connected';
            return $data;
        }
        // Comprobamos si el usuario es dueño del thread 
        if($this->model->is_user_thread_owner($id, $this->userController->get_user_id()) == false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_is_not_the_owner';
            return $data;
        }
        // Comprueba si algún dato está vacío
        if (empty($id) || empty($title) || empty($msg)) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'empty_data';
            return $data;
        }
        // Comprueba que los datos no midan más de lo estipulado
        $trimmedMsg = strip_tags($msg);
        if(strlen($title) > 100 || strlen($trimmedMsg) > 2000) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=incorrect_length';
            return $data;
        }

        // Manda la orden al modelo para editar el thread
        $data = [];
        $r = $this->model->edit_thread($id, $title, $msg);

        // Si la respuesta es que el thread no existe, manda el siguiente error
        if($r === 'thread_id_does_not_exist') {
            return [ "status" => 1, "error" => "thread_id_does_not_exist" ];
        }
        // Si la respuesta es satisfactoria redirige a la vista correspondiente
        return [ "status" => 0, "redirectUrl" => "index.php?view=threads&section=$id&msg=thread_edit_success" ];
    }
    
    // Eliminar un thread
    public function delete_thread($id) {

        // Comprueba si el usuario está conectado
        if(!$this->userController->get_is_connected()) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_connected';
            return $data;
        }
        // Comprueba si el usuario es dueño del thread
        if($this->model->is_user_thread_owner($id, $this->userController->get_user_id()) == false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_owner';
            return $data;
        }

        // Manda la orden al modelo para eliminar el thread
        $data = [];
        $r = $this->model->delete_thread($id, $this->userController->get_user_id());
       
        // Si la respuesta es que el thread no existe
        if($r === 'thread_id_does_not_exist') {
            $data['status'] = 1;
            $data['error'] = 'thread_id_does_not_exist';
            return $data;
        }

        // Si la respuesta es true
        $data['status'] = 0;
        $data['redirectUrl'] = "index.php";
        return $data;
    }

    // Obtner la información de un thread
    public function get_thread ($thread_id) {
        if(empty($thread_id)) {
           return [
                "status" => 1,
                "msg" => 'error',
                "error" => 'empty_thread_id',
                "redirectUrl" => 'index.php'
            ];
        }
        // Manda la orden al modelo para obtener el data de un thread
        $r = $this->model->get_thread($thread_id);
        // Si la respuesta es que el thread no existe
        if($r === 'thread_not_found' ) {
            return [
                "status" => 1,
                "msg" => 'error',
                "error" => 'thread_not_found',
                "redirectUrl" => 'index.php'
            ];
        }
        // Retorna la respuesta
        return $r;
    }

    // Obtener la información de todos los posts dentro de un thread
    public function get_posts_thread($thread_id) {
        return $this->model->get_posts_thread($thread_id);
    }

    // Obtener el número de posts o replies que contiene un thread
    public function count_thread_posts($thread_id) {
        return $this->model->count_thread_posts($thread_id);
    }

    // ____           _       
    // |  _ \ ___  ___| |_ ___ 
    // | |_) / _ \/ __| __/ __|
    // |  __/ (_) \__ \ |_\__ \
    // |_|   \___/|___/\__|___/

    // Crear un post
    public function create_post($section_id, $thread_id, $user_id, $msg) {
        // Comprueba si el usuario está conectado
        if(!$this->userController->get_is_connected()) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_connected';
            return $data;
        }
        // Comprueba si alguno de los datos está vacío
        if (empty($section_id) || empty($thread_id) || empty($user_id) || empty($msg)) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'empty_data';
            return $data;
        }
        // Comprueba que el msg no mida más de lo estipulado
        $trimmedMsg = strip_tags($msg);
        if(strlen($trimmedMsg) > 2000) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=incorrect_length';
            return $data;
        }
        
        // Manda la orden al modelo para que se cree el post
        $data = [];
        $r = $this->model->create_post($section_id, $thread_id, $user_id, $msg);

        // Si la respuesta es false
        if($r === false) {
            $data['status'] = 1;
            $data['redirectUrl'] = "index.php?view=posts&thread=$thread_id&msg=post_created_error";
            return $data;
        }
        // Si la respuesta es true
        $data['status'] = 0;
        $data['redirectUrl'] = "index.php?view=posts&thread=$thread_id&msg=post_created_success";
        return $data;
    }

    // Editar un post
    public function edit_post($post_id, $msg) {
        
        // Comprueba si el usuario está conectado
        if(!$this->userController->get_is_connected()) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_connected';
            return $data;
        }
        // Comprueba si el usuario es dueño del post 
        if($this->model->is_user_post_owner($post_id, $this->userController->get_user_id()) == false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_is_not_the_owner';
            return $data;
        }
        // Comprueba si está vacío
        if (empty($post_id) || empty($msg)) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'empty_data';
            return $data;
        }
        // Comprueba que el msg no mida más de lo estipulado
        $trimmedMsg = strip_tags($msg);
        if(strlen($trimmedMsg) > 2000) {
            $data['status'] = 1;
            $data['redirectUrl'] = 'index.php?view=home&error=incorrect_length';
            return $data;
        }

        // Manda la orden al modelo para que edite el post
        $data = [];
        $r = $this->model->edit_post($post_id, $msg);

        // Si la respuesta es que el post no exite
        if($r === 'post_not_exist') {
            return [ "status" => 1, "error" => "post_not_exist" ];
        }

        return [ "status" => 0, "redirectUrl" => "index.php?view=posts" ];
    }

    // Eliminar un post
    public function delete_post($post_id) {

        // Comprueba si el usuario está conectado
        if(!$this->userController->get_is_connected()) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_connected';
            return $data;
        }
        // Comprueba si el usuario es dueño del thread
        if($this->model->is_user_post_owner($post_id, $this->userController->get_user_id()) == false) {
            $data['status'] = 1;
            $data['msg'] = 'error';
            $data['error'] = 'user_not_owner';
            return $data;
        }

        // Manda la orden al modelo para que elimine el post
        $data = [];
        $r = $this->model->delete_post($post_id, $this->userController->get_user_id());
       
        // Si la respuesta es que el post no existe
        if($r === 'post_not_exist') {
            $data['status'] = 1;
            $data['error'] = 'post_not_exist';
            return $data;
        }

        $data['status'] = 0;
        return $data;
    }                    
    
    // Obtener la información de un post
    public function get_post ($post_id) {
       // Comprueba si el post contiene un id
        if(empty($post_id)) {
            return false;
            throw new Error('Empty Post Id');
        }
        return $this->model->get_post($post_id);
    }
    
    //  ____             __ _ _      
    // |  _ \ _ __ ___  / _(_) | ___ 
    // | |_) | '__/ _ \| |_| | |/ _ \
    // |  __/| | | (_) |  _| | |  __/
    // |_|   |_|  \___/|_| |_|_|\___|

    // Obtener el nombre de usuario por su id
    public function get_username_by_user_id($user_id) {
        // Manda la orden al modelo para obtener el nombre de usuario
        $username = $this->model->get_username_by_user_id($user_id);
        // Si no existe nombre de usuario
        if($username === false) {
            return "error_username";
        }
        // Retorna el nombre de usuario
        return  $username;
    }

    // Obtener la fecha de registro de un usuario por su id
    public function get_joined_date_by_user_id($user_id) {
        // Manda la orden al modelo para obtener la fecha de regisgtro del usuario
        $joined_date = $this->model->get_joined_date_by_user_id($user_id);
        // Si no se encuentra la fecha
        if($joined_date === false) {
            return "error_joined_date";
        }
        // Retorna la fecha
        return  $joined_date;
    }
}
?>