<?php
class UserModel{
    static public function readUser($user_id=null){
        $data = [];
        $query = 'SELECT user_id,user_name,user_lastName,user_email,user_pass,user_phone FROM users';
        if($user_id > 0 && $user_id != null){
            $data['user_id'] = $user_id;
            $query .= ' WHERE user_id =:user_id';
        }
        return self::executeQuery($query,201,$data);
    }
    //POST
    static public function createUser($dataIn){
        if(!(self::emailExist($dataIn))){
            $query = "INSERT INTO `users`(`user_name`, `user_lastName`, `user_email`, `user_pass`, `user_phone`, `us_identifier`, `us_key`) VALUES (:user_name,:user_lastName,:user_email,:user_pass,:user_phone,:us_identifier,:us_key)";
            return self::executeQuery($query,200,$dataIn);
        }
        return 209;
    }

    //PUT
    static public function updateUser($id,$dataIn){
        $data = $dataIn;
        $data['user_id']=$id;
        if(self::idExist($data)){
            if(array_key_exists('user_email',$data)){
                if(self::emailExist($data)){
                    return 209;
                }
            }
            $query = "UPDATE users SET ";
            $dataAO = new ArrayObject($data);
            $iter = $dataAO->getIterator();
            while($iter->valid()){
                $query .= $iter->key()."=:".$iter->key();
                $iter->next();
                if($iter->valid()){
                    $query .= ",";
                }
                else{
                    $query .= " WHERE user_id =:user_id";
                }
            }
            return self::executeQuery($query,202,$data);
        }
        return 219;
    }

    //DELETE
    static public function deleteUser($id){
        $data['user_id']=$id;
        if(self::idExist($data)){
            $query = 'DELETE FROM users WHERE user_id = :user_id';
            return self::executeQuery($query,203,$data);
        }
        return 219;
    }
    
    static public function deleteAllUsers(){
        $query = 'DELETE FROM users';
        $reset = 'ALTER TABLE users AUTO_INCREMENT = 1';
        self::executeQuery($reset,211);
        return self::executeQuery($query,210);
    }

    //Login
    static public function login($dataIn){
        if(self::emailExist($dataIn)){
            $query = 'SELECT us_identifier,us_key FROM users WHERE user_email=:user_email AND user_pass=:user_pass';
            $count = self::executeQuery($query,1,$dataIn)[1]->rowCount();
            if($count>0){
                $response = self::executeQuery($query,600,$dataIn,true);
                //var_dump($response);
                return $response;
            }
            return 604;
        }
        return 604;
    }

    //Extras
    static private function emailExist($data){
        $query = "SELECT user_email FROM users WHERE user_email=:user_email";
        $count = self::executeQuery($query,200,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    static public function idExist($data){
        $query = "SELECT user_id FROM users WHERE user_id=:user_id";
        $count = self::executeQuery($query,200,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array('user_id','user_name','user_lastName','user_email','user_pass','user_phone','user_age','us_identifier','us_key');
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":user_id", $data["user_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":user_name", $data["user_name"],PDO::PARAM_STR);
                        break;
                    case 2:
                        $statement->bindParam(":user_lastName", $data["user_lastName"],PDO::PARAM_STR);
                        break;
                    case 3:
                        $statement->bindParam(":user_email", $data["user_email"],PDO::PARAM_STR);
                        break;
                    case 4:
                        $statement->bindParam(":user_pass", $data["user_pass"],PDO::PARAM_STR);
                        break;
                    case 5:
                        $statement->bindParam(":user_phone", $data["user_phone"],PDO::PARAM_STR);
                        break;
                    case 6:
                        $statement->bindParam(":user_age", $data["user_age"],PDO::PARAM_INT);
                        break;
                    case 7:
                        $statement->bindParam(":us_identifier", $data["us_identifier"],PDO::PARAM_STR);
                        break;
                    case 8:
                        $statement->bindParam(":us_key", $data["us_key"],PDO::PARAM_STR);
                        break;
                }
            }
        }

        if(preg_match('/^SELECT.*$/',$query)){
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            if($error != false) return array(910,$error->getMessage());
            if($fetch) return array($confirmCod,$statement->fetchAll());
            return array($confirmCod,$statement);
        }
        else{
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            $statement-> closeCursor();
            $statement = null;
            if($error != false) return array(910,$error->getMessage());
            else return $confirmCod;
        }
    }
}
?>