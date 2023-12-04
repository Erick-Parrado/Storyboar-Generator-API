<?php
class UserModel{
    static public function createUser($data){
            if(!(self::emailExist($data))){
                $query = "INSERT INTO `users`(`user_name`, `user_lastName`, `user_email`, `user_pass`, `user_phone`, `user_age`, `us_identifier`, `us_key`) VALUES (:user_name,:user_lastName,:user_email,:user_pass,:user_phone,:user_age,:us_identifier,:us_key)";
                if(self::executeQuery($query,$data)) return 910;
                return 200;
            }
            return 203;
    }

    static private function emailExist($data){
        $query = "SELECT user_email FROM users WHERE user_email=:user_email";
        $count = self::executeQuery($query,$data)->rowCount();
        return ($count>0)?1:0;
    }

    static public function  executeQuery($query,$data=null,$fetch=false){
        $fields = array('user_id','user_name','user_lastName','user_email','user_pass','user_phone','user_age','us_identifier','us_key');
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":user_id", $data["user_id"],PDO::PARAM_STR);
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
            $statement -> execute();
            if($fetch) return $statement->fetchAll(PDO::FETCH_ASSOC);
            return $statement;
        }
        else{
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            $statement-> closeCursor();
            $statement = null;
            if($error != false) return false;
            else return true;
        }
    }
}
?>