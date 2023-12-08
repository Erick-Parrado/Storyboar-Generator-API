<?php
class ProjectModel{
    //POST
    static public function createProject($data){
        if(!self::projectExist($data)){
            $data['proj_pin'] = self::generatePIN();
            $data['proj_dateUpdate'] = self::makeUpdate();
            $query = 'INSERT INTO projects(proj_tittle,proj_producer,proj_description,proj_pin,proj_dateUpdate) VALUES (:proj_tittle,:proj_producer,:proj_description,:proj_pin,:proj_dateUpdate)';
            return self::executeQuery($query,300,$data);
        }
        return 309;
    }
    
    //GET
    static public function readProject($proj_id=null){
        $data = [];
        $query = 'SELECT proj_id,proj_tittle,proj_producer,proj_description,proj_dateUpdate FROM projects';
        if($proj_id > 0 && $proj_id != null){
            $data['proj_id'] = $proj_id;
            $query .= ' WHERE proj_id =:proj_id';
        }
        return self::executeQuery($query,301,$data);
    }

    //PUT
    static public function updateProject($id,$dataIn){
        $data = $dataIn;
        $data['proj_id'] = $id;
        if(self::exist($data)){
            $data['proj_dateUpdate'] = self::makeUpdate();
            $query = "UPDATE projects SET ";
            $dataAO = new ArrayObject($data);
            $iter = $dataAO->getIterator();
            while($iter->valid()){
                $query .= $iter->key()."=:".$iter->key();
                $iter->next();
                if($iter->valid()){
                    $query .= ",";
                }
                else{
                    $query .= " WHERE proj_id =:proj_id";
                }
            }
            return self::executeQuery($query,302,$data);
        }
        return 319;
    }

    //DELETE
    static public function deleteProject($id){
        $data['proj_id'] = $id;
        if(self::exist($data)){
            $query = "DELETE FROM projects WHERE proj_id = :proj_id";
            return self::executeQuery($query,303,$data);
        }
        return 319;
    }
    //Extras
    static public function exist($data){
        $query = "SELECT proj_id FROM projects WHERE proj_id=:proj_id";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    static public function projectExist($data){
        $query = "SELECT proj_id FROM projects WHERE proj_producer=:proj_producer AND proj_tittle=:proj_tittle";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }
    
    static public function pinExist($pin){
        $data = [];
        if(!is_array($pin)){
            $data['proj_pin'] = $pin;
        }
        else{
            $data = $pin;
        }
        $query = "SELECT proj_pin FROM projects WHERE proj_pin=:proj_pin";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    static public function projectByPIN($pin){
        $data =[];
        if(!is_array($pin)){
            $data['proj_pin'] = $pin;
        }
        else{
            $data = $pin;
        }
        if(self::pinExist($data)){
            $query = 'SELECT proj_id FROM projects WHERE proj_pin=:proj_pin';
            $project = self::executeQuery($query,1,$data,true)[1][0]['proj_id'];
            return $project;
        }
        return false;
    }

    static public function generatePIN($proj_id = null){
        do{
            $pin = strtoupper(bin2hex(random_bytes(4)));
        }while(ProjectModel::pinExist($pin));
        
        $data['proj_pin'] = $pin;
        if($proj_id==null){
            return $data['proj_pin'];
        }
        $result = self::updateProject($proj_id,$data);
        if($result == 302) return 304;
        return $result;
    }

    static public function makeUpdate($proj_id = null){
        $data['proj_dateUpdate'] = date('d/m/Y', time());
        if($proj_id==null){
            return $data['proj_dateUpdate'];
        }
        $result = self::updateProject($proj_id,$data);
        if($result == 302) return 304;
        return $result;
    }

    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array('proj_id','proj_tittle','proj_producer','proj_description','proj_dateUpdate','proj_pin');
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":proj_tittle", $data["proj_tittle"],PDO::PARAM_STR);
                        break;
                    case 2:
                        $statement->bindParam(":proj_producer", $data["proj_producer"],PDO::PARAM_STR);
                        break;
                    case 3:
                        $statement->bindParam(":proj_description", $data["proj_description"],PDO::PARAM_STR);
                        break;
                    case 4:
                        $statement->bindParam(":proj_dateUpdate", $data["proj_dateUpdate"],PDO::PARAM_STR);
                        break;
                    case 5:
                        $statement->bindParam(":proj_pin", $data["proj_pin"],PDO::PARAM_STR);
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