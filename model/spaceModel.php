<?php
class SpaceModel{
    static public function readSpaces($spac_id){
        $data= [];
        $query = 'SELECT * FROM spaces';
        if($spac_id > 0 && $spac_id != null){
            $data['spac_id'] = $spac_id;
            $query .= ' WHERE spac_id =:spac_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data){
        if(array_key_exists('spac_id',$data)){
            $query = "SELECT spac_id FROM spaces WHERE spac_id=:spac_id";
            $count = self::executeQuery($query,1,$data)[1]->rowCount();
            if($count<0) throw new Exception(425);
        }
    }

    static public function executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            'spac_id',
            'spac_name',
            'spac_abr'
        );
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":spac_id", $data["spac_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":spac_name", $data["spac_name"],PDO::PARAM_STR);
                        break;
                    case 1:
                        $statement->bindParam(":spac_abr", $data["spac_standard"],PDO::PARAM_STR);
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