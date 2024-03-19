<?php
//  _____ ___  ____  _   _ __  __   __  __  ___  ____  _____ _     
// |  ___/ _ \|  _ \| | | |  \/  | |  \/  |/ _ \|  _ \| ____| |    
// | |_ | | | | |_) | | | | |\/| | | |\/| | | | | | | |  _| | |    
// |  _|| |_| |  _ <| |_| | |  | | | |  | | |_| | |_| | |___| |___ 
// |_|   \___/|_| \_\\___/|_|  |_| |_|  |_|\___/|____/|_____|_____|
                                                                
    class ForumModel {

        private $conn;
        public $user_id;
                                        
        public function __construct($db) {
            $this->conn = $db;
        }

        //  ____            _   _                 
        // / ___|  ___  ___| |_(_) ___  _ __  ___ 
        // \___ \ / _ \/ __| __| |/ _ \| '_ \/ __|
        //  ___) |  __/ (__| |_| | (_) | | | \__ \
        // |____/ \___|\___|\__|_|\___/|_| |_|___/                                 

        // Comprueba si una sección existe
        function does_section_exist($section_id) {
            // Preparación se la query
            $query = "SELECT id FROM sections WHERE id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);
            // Ejecución de la query
            $stmt->execute();

            // Comprueba si se devuelve alguna fila
            if($stmt->rowCount() > 0) {
                // El nombre de la sección existe en la DB
                return true;
            }
            // El nombre de la sección no existe en la DB
            return false;
        }

        // Crear una sección
        function create_section($title, $description, $user_id) {
            // Comprueba si la sección existe
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

        // Editar una sección
        function edit_section($id, $title, $description){
            // Comprueba si la sección existe
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

        //  Eliminar una sección
        function delete_section($section_id) {
            // Comprueba si la sección existe
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

        // Obtener data de todas las secciones
        public function get_sections() {
            $query = "SELECT * FROM sections";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Retornamos un array con toda la información de todas las secciones
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            // Retornamos un array vacío
            return [];
        }

        // Obtener data de una sección
        public function get_section_data($section_id) {
            if(!$this->does_section_exist($section_id)) {
                return "section_not_exist";
            }
            
            $query = "SELECT * FROM sections WHERE id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);
            $stmt->execute();
            // Retornamos un array con toda la información de una sección
            $section = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            return $section;
        }

        // Obtener los threads de una sección
        public function get_threads_section($section_id) {
            $query = "SELECT * FROM threads WHERE section_id=:section_id ORDER BY creation_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Retornamos un array con toda la información de todos los threads de una sección
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        }

        // Contar el número de threads en una sección
        public function count_section_threads($section_id) {
            $query = "SELECT COUNT(*) as count FROM threads WHERE section_id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);

            if ($stmt->execute()) {
                // Retorna el número de threads que tiene una sección
                return ($stmt->fetchAll(PDO::FETCH_ASSOC)[0])['count'];
            }
            return 0;
        }

        // Contar el número de posts en una sección
        public function count_section_posts($section_id) {
            $query = "SELECT COUNT(*) as count FROM posts WHERE section_id=:section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);

            if ($stmt->execute()) {
                // Retorna el número de posts qie tiene una sección
                return ($stmt->fetchAll(PDO::FETCH_ASSOC)[0])['count'];
            }
            return 0;
        }
        
        //  _____ _                        _     
        // |_   _| |__  _ __ ___  __ _  __| |___ 
        //   | | | '_ \| '__/ _ \/ _` |/ _` / __|
        //   | | | | | | | |  __/ (_| | (_| \__ \
        //   |_| |_| |_|_|  \___|\__,_|\__,_|___/
        
        // Comprueba si un thread existe
        public function does_thread_exist($thread_id) {
            // Comprueba que el id del thread no esté vacío
            if(empty($thread_id)) {
                throw new Error("Thread id cannot be empty");
                return false;
            }

            $query = "SELECT id FROM threads WHERE id=:thread_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        }

        // Comprueba si el usuario logeado es el creador del thread
        public function is_user_thread_owner($thread_id, $user_id) {
            $query = "SELECT id FROM threads WHERE id=:thread_id AND user_id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                return true;
            }
            return false;
        }

        // Crear un thread
        public function create_thread($title, $msg, $section_id, $user_id) {
            // Comprueba si la sección donde se quiere crear el thread existe
            if($this->does_section_exist($section_id) == false) {
                return false;
            }
            
            $query = "INSERT INTO threads SET title=:title, msg=:msg, section_id=:section_id, user_id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":msg", $msg);
            $stmt->bindParam(":section_id", $section_id);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {
                // Si el thread se ha creado correctamente
                return true;
            }
            // Si ha ocurrido algún problema
            return false;
        }

        // Editar un thread
        public function edit_thread($id, $title, $msg){
            // Comprueba si el thread existe
            if($this->does_thread_exist($id) === false) {
                return "thread_id_does_not_exist";
            }

            $query = "UPDATE threads SET title=:title, msg=:msg WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":msg", $msg);
            $stmt->bindParam(":id", $id);
            if($stmt->execute()) {return true;}
            return false;
        }

        // Eliminar todos los post dentro de un thread
        public function delete_posts_from_thread($thread_id) {
            // Comprueba si el thread existe
            if($this->does_thread_exist($thread_id) === false) {
                return "thread_id_does_not_exist";
            }

            $query = "DELETE FROM posts WHERE thread_id=:thread_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->execute();
        }

        // Eliminar un thread
        public function delete_thread($thread_id, $user_id) {
            // Comprueba si el thread existe
            if($this->does_thread_exist($thread_id) === false) {
                return "thread_id_does_not_exist";
            }
            // Elimina los posts que existen dentro del thread
            $this->delete_posts_from_thread($thread_id);

            $query = "DELETE FROM threads WHERE id=:thread_id AND user_id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {return true;}
            return false;
        }

        // Obtener data de un thread
        public function get_thread($thread_id) {
            // Comprobar si el thread existe
            if(!$this->does_thread_exist($thread_id)){
                return 'thread_not_found';
            }

            $query = "SELECT * FROM threads WHERE id=:thread_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->execute();
            // Retorna un array con toda la información de un thread
            $thread = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            return $thread;
        }

        // Obtener los replies o posts de un thread
        public function get_posts_thread($thread_id) {
            $query = "SELECT * FROM posts WHERE thread_id=:thread_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Retorna un array con toda la información de todos los posts dentro de un thread
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            // Retorna un array vacío
            return [];
        }
        
        // Contar el número de replies o posts que tiene un thread
        public function count_thread_posts($thread_id) {
            $query = "SELECT COUNT(*) as count FROM posts WHERE thread_id=:thread_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":thread_id", $thread_id);

            if ($stmt->execute()) {
                // Retorna el número de posts dentro de un thread
                return ($stmt->fetchAll(PDO::FETCH_ASSOC)[0])['count'];
            }
            // Retorna 0
            return 0;
        }

        //  ____           _       
        // |  _ \ ___  ___| |_ ___ 
        // | |_) / _ \/ __| __/ __|
        // |  __/ (_) \__ \ |_\__ \
        // |_|   \___/|___/\__|___/
       
        // Comprueba si un post existe
        public function does_post_exist($post_id) {
            // Comprueba que el id del post  no está vacío
            if(empty($post_id)) {
                return false;
            }

            $query = "SELECT id FROM posts WHERE id=:post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->execute();
            if($stmt->rowCount() > 0) {return true;}
            return false;
        }

        // Comprueba si el usuario logeado es el creador del post
        public function is_user_post_owner($post_id, $user_id) {
            $query = "SELECT id FROM posts WHERE id=:post_id AND user_id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            if($stmt->rowCount() > 0) {return true;}
            return false;
        }

        // Crear un post
        public function create_post($section_id, $thread_id, $user_id, $msg) {
            // Comprueba si el thread donde se quiere crear el post existe
            if($this->does_thread_exist($thread_id) == false) {
                return false;
            }
            
            $query = "INSERT INTO posts SET section_id=:section_id, thread_id=:thread_id, user_id=:user_id, msg=:msg";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":section_id", $section_id);
            $stmt->bindParam(":thread_id", $thread_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":msg", $msg);
            if($stmt->execute()) {return true;}
            return false;
        }

        // Editar un post
        public function edit_post($post_id, $msg){
            // Comprueba si el post que se quiere editar existe
            if($this->does_post_exist($post_id) === false) {
                return "post_not_exist";
            }

            $query = "UPDATE posts SET msg=:msg WHERE id=:post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":msg", $msg);
            $stmt->bindParam(":post_id", $post_id);
            if($stmt->execute()) {return true;}
            return false;
        }

        // Eliminar un post
        public function delete_post($post_id, $user_id) {
            // Comprueba si el post que se quiere eliminar existe
            if($this->does_post_exist($post_id) === false) {
                return "post_not_exist";
            }
         
            $query = "DELETE FROM posts WHERE id=:post_id AND user_id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {return true;}
            return false;
        }

        // Obtener data de un post
        public function get_post ($post_id) {
            $query = "SELECT * FROM posts WHERE id=:post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->execute();
            // Retorna un array con toda la información de un post
            $post = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            return $post;
        }

        //  ____             __ _ _      
        // |  _ \ _ __ ___  / _(_) | ___ 
        // | |_) | '__/ _ \| |_| | |/ _ \
        // |  __/| | | (_) |  _| | |  __/
        // |_|   |_|  \___/|_| |_|_|\___|
                                      
        // Obtener el nombre de usuario por la id del usuario
        public function get_username_by_user_id($user_id) {
            $query = "SELECT username FROM users WHERE id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Retornamos el nombre de usuario
                return ($stmt->fetchAll(PDO::FETCH_ASSOC)[0])['username'];
            }
            return false;
        }

        // Obtener la fecha en la que se inscribió el usuario
        public function get_joined_date_by_user_id($user_id) {
            $query = "SELECT registration_date FROM users WHERE id=:user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                // Retorna la fecha de registro del usuario
                return ($stmt->fetchAll(PDO::FETCH_ASSOC)[0])['registration_date'];
            }
            return false;
        }

    }
 
?>