<?php
class DayTimesModel{
    static public function readDayTimes($dayT_id){
        $data= [];
        $query = 'SELECT * FROM day_times';
        if($dayT_id > 0 && $dayT_id != null){
            $data['dayT_id'] = $dayT_id;
            $query .= ' WHERE dayT_id =:dayT_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data){
        $query = "SELECT dayT_id FROM day_times WHERE dayT_id=:dayT_id";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    static public function executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            'dayT_id',
            'dayT_name',
            'dayT_standard'
        );
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":dayT_id", $data["dayT_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":dayT_name", $data["dayT_name"],PDO::PARAM_STR);
                        break;
                    case 1:
                        $statement->bindParam(":dayT_standard", $data["dayT_standard"],PDO::PARAM_STR);
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