<?php
    require_once 'config.php';

    class Connection{
        static public function doConnection(){
            $con = false;
            try{
                $data = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
                $con = new PDO($data,DB_USER,DB_PASSWORD);
                return $con;
            }
            catch(PDOException $e){
                ResponseController::response(909,$e->getMessage());
                return false;
            }
        }
    }
?>