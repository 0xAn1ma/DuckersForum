<?php

class DataController {
    public static function generateData($status, $msg, $redirectUrl, $data = []) {
        return [
            "status" => $status,
            "msg" => $msg,
            "redirectUrl" => $redirectUrl,
            "data" => $data
        ];
    }

    public static function decodeJson() {
        return json_decode(file_get_contents("php://input"));
    }

    public static function returnJson($jsonData) {
        header('Content-Type: application/json');
        echo(json_encode($jsonData));
    }

    public static function debugData($data) {
        echo '<pre>' , var_dump($data) , '</pre>';
    }
}

?>