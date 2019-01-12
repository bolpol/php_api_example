<?php
/**
 * Created by PhpStorm.
 * User: pirno
 * Date: 10/6/2018
 * Time: 9:17 PM
 */

class API
{
    /**
     * @param $status
     * @param $message
     * @param $result
     * @return string
     */
    public function response($status, $message, $result) {
        header("HTTP/1.0 200 Ok");
        header('Content-Type: application/json');
        return json_encode([
            "status" => $status,
            "message" => $message,
            "result" => $result
        ]);
    }
}