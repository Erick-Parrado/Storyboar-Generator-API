<?php

require_once 'model/tableModel.php';

class UserModel extends TableModel{
    public function __construct()
    {
        $table_name = 'users';
        $table_prefix = 'user';
        $table_fields = array(
            'user_id',
            'user_name',
            'user_lastName',
            'user_email',
            'user_pass',
            'user_phone',
            'user_age',
            'us_identifier',
            'us_key'
        );
        $model_baseCode = 200;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    //GET
    static public function readUser($user_id=0){
        $data = [];
        $query = 'SELECT user_id,user_name,user_lastName,user_email,user_pass,user_phone FROM users';
        if($user_id > 0 && $user_id != null){
            $data['user_id'] = $user_id;
            $query .= ' WHERE user_id =:user_id';
        }
        return self::executeQuery($query,201,$data);
    }
    //POST
    static public function createUser($data){
        self::emailExist($data);
        $data = self::generateSalting($data);
        $query = "INSERT INTO users(user_name, user_lastName, user_email, user_pass, user_phone, us_identifier, us_key) VALUES (:user_name,:user_lastName,:user_email,:user_pass,:user_phone,:us_identifier,:us_key)";
        return self::executeQuery($query,200,$data);
    }

    //PUT
    static public function updateUser($id,$data){
        $data['user_id']=$id;
        self::exist($data);
        self::emailExist($data);
        $data = self::generateSalting($data);
        new UserModel();
        parent::updateMethod($data);
        return 202;
        throw new Exception(219);
    }

    //DELETE
    static public function deleteUser($id){
        $data['user_id']=$id;
        self::exist($data);
        $query = 'DELETE FROM users WHERE user_id = :user_id';
        return self::executeQuery($query,203,$data);
        return 219;
    }

    //Login
    static public function login($dataIn){
        self::emailExist($dataIn);
        $query = 'SELECT user_id,us_identifier,us_key FROM users WHERE user_email=:user_email AND user_pass=:user_pass';
        $count = self::executeQuery($query,1,$dataIn)[1]->rowCount();
        if($count>0){
            $response = self::executeQuery($query,600,$dataIn,true);
            //var_dump($response);
            return $response;
        }
        throw new Exception(604);
    }

    static public function validateAuth($data){
        $query = 'SELECT user_id FROM users WHERE us_identifier=:us_identifier AND us_key=:us_key';
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        if($count<=0) throw new Exception(115);
        return true;
    }

    //Extras
    static private function emailExist($data){
        if(array_key_exists('user_email',$data)){
            $query = "SELECT user_email FROM users WHERE user_email=:user_email";
            $count = self::executeQuery($query,200,$data)[1]->rowCount();
            if($count<0) throw new Exception(209);
        }
    }

    static public function exist($data,$way=false){
        new UserModel();
        parent::exist($data,$way);
    }
    
    static private function generateSalting($data){
        $trimmed_data="";
        if(($data !="") || (!empty($data))){
            $trimmed_data = array_map('trim', $data);
            if(isset($data['user_pass'])){
                $key = crypt($trimmed_data['user_pass'],'$1$story$board$Generator$');
                $key = str_replace("$","W4rT",$key);
                $trimmed_data['us_key']=$key;
            }
            if(isset($data['user_email'])){
                $identifier = crypt($trimmed_data['user_email'],'$1$Wasaaaa$');
                $identifier = str_replace("$","y78",$identifier);
                $trimmed_data['us_identifier']=$identifier;
            }
            return $trimmed_data;
        }
    }

    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new UserModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }
    
    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "user_id":
                    $statement->bindParam(":user_id", $data["user_id"],PDO::PARAM_INT);
                    break;
                case "user_name":
                    $statement->bindParam(":user_name", $data["user_name"],PDO::PARAM_STR);
                    break;
                case "user_lastName":
                    $statement->bindParam(":user_lastName", $data["user_lastName"],PDO::PARAM_STR);
                    break;
                case "user_email":
                    $statement->bindParam(":user_email", $data["user_email"],PDO::PARAM_STR);
                    break;
                case "user_pass":
                    $statement->bindParam(":user_pass", $data["user_pass"],PDO::PARAM_STR);
                    break;
                case "user_phone":
                    $statement->bindParam(":user_phone", $data["user_phone"],PDO::PARAM_STR);
                    break;
                case "user_age":
                    $statement->bindParam(":user_age", $data["user_age"],PDO::PARAM_INT);
                    break;
                case "us_identifier":
                    $statement->bindParam(":us_identifier", $data["us_identifier"],PDO::PARAM_STR);
                    break;
                case "us_key":
                    $statement->bindParam(":us_key", $data["us_key"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>