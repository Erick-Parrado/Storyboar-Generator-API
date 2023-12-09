<?php
class FramingModel{
    static public function readFraming($fram_id){
        $data= [];
        $query = 'SELECT * FROM  framings';
        if($fram_id > 0 && $fram_id != null){
            $data['fram_id'] = $fram_id;
            $query .= ' WHERE fram_id =:fram_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data){
        if(array_key_exists('fram_id',$data)){
            $query = "SELECT fram_id FROM framings WHERE fram_id=:fram_id";
            $count = self::executeQuery($query,1,$data)[1]->rowCount();
            if($count<0) throw new Exception(525);
        }
    }

    static public function executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            'fram_id',
            'fram_name',
            'fram_abr'
        );
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":fram_id", $data["fram_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":fram_name", $data["fram_name"],PDO::PARAM_STR);
                        break;
                    case 1:
                        $statement->bindParam(":fram_abr", $data["fram_standard"],PDO::PARAM_STR);
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