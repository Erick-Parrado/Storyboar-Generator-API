<?php
class MoveModel{
    static public function readMoves($move_id){
        $data= [];
        $query = 'SELECT * FROM moves';
        if($move_id > 0 && $move_id != null){
            $data['move_id'] = $move_id;
            $query .= ' WHERE move_id =:move_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data){
        if(array_key_exists('move_id',$data)){
            $query = "SELECT move_id FROM moves WHERE move_id=:move_id";
            $count = self::executeQuery($query,1,$data)[1]->rowCount();
            if($count<=0) throw new Exception(526);
        }
    }

    static public function executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            'move_id',
            'move_name'
        );
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":move_id", $data["move_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":move_name", $data["move_name"],PDO::PARAM_STR);
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