<?php
class ShotModel{
    static public function readShots($shot_id){
        $data= [];
        $query = 'SELECT * FROM shots';
        if($shot_id > 0 && $shot_id != null){
            $data['shot_id'] = $shot_id;
            $query .= ' WHERE shot_id =:shot_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data){
        if(array_key_exists('shot_id',$data)){
            $query = "SELECT shot_id FROM shots WHERE shot_id=:shot_id";
            $count = self::executeQuery($query,1,$data)[1]->rowCount();
            if($count<=0) throw new Exception(426);
        }
    }

    static public function executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            'shot_id',
            'shot_name'
        );
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":shot_id", $data["shot_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":shot_name", $data["shot_name"],PDO::PARAM_STR);
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