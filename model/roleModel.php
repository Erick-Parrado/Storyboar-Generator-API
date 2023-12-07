<?php
class RoleModel{
    static public function readRoles($role_id){
        $data= [];
        $query = 'SELECT role_id,role_name FROM roles';
        if($role_id > 0 && $role_id != null){
            $data['role_id'] = $role_id;
            $query .= ' WHERE role_id =:role_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function idExist($data){
        $query = "SELECT role_id FROM roles WHERE role_id=:role_id";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array('role_id','role_name');
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":role_id", $data["role_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":role_name", $data["role_name"],PDO::PARAM_STR);
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