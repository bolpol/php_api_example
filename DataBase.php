<?php
/**
 * Created by PhpStorm.
 * User: pirno
 * Date: 10/5/2018
 * Time: 11:46 PM
 */

/**
 * Class APIDataBase - Список адрессов и т.д.
 */
class DataBase {
    public $db_config;
    function __construct()
    {
        try
        {
            $this->db_config = new PDO('mysql:dbname=' . MYSQL['name'] . ';host=' . MYSQL['host'], MYSQL['user'], MYSQL['pass']);
            $this->db_config->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
}

