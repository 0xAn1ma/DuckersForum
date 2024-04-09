<?php
    class ForumUtils {
        public static function isForumInstalled() {
            if (file_exists('config/config.php'))  {
                return true;
            }
            return false;
        }
    }
?>
