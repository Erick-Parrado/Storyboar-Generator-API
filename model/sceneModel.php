<?php

require_once 'model/projectModel.php';
require_once 'model/spaceModel.php';
require_once 'model/dayTimeModel.php';

class SceneModel{

    //GET
    static public function readScene($scen_number,$proj_id){
        $data['scen_number'] = $scen_number;
        $data['proj_id'] = $proj_id;
        $data['scen_id']= self::exist($data);
        $query = 'SELECT scen_id,scen_number,scen_duration,scen_place,dayT_id,spac_id,scen_argument,proj_id FROM scenes WHERE scen_id =:scen_id';
        return self::executeQuery($query,301,$data);
    }
    
    static public function readProjectScenes($proj_id = null){
        if($proj_id == null) throw new Exception(428);
        $data['proj_id'] = $proj_id;
        $query = 'SELECT scen_id,scen_number,scen_duration,scen_place,dayT_id,spac_id,scen_argument,proj_id FROM scenes WHERE proj_id =:proj_id ORDER BY scen_number ASC';
        return self::executeQuery($query,402,$data);
    }

    //POST
    static public function createScene($proj_id,$data){
        $data['proj_id'] = $proj_id;
        SpaceModel::exist($data);
        DayTimeModel::exist($data);
        if(!self::validNumberScene($data)) return 421;
        self::newNumberScene($data); 
        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
        $query = 'INSERT INTO `scenes`(`scen_number`, `scen_duration`, `scen_place`, `dayT_id`, `spac_id`, `scen_argument`, `proj_id`) VALUES  (:scen_number,:scen_duration,:scen_place,:dayT_id,:spac_id,:scen_argument,:proj_id)';
        return self::executeQuery($query,400,$data);
    }

    //PUT
    static public function updateScene($scen_number,$proj_id,$data){
        SpaceModel::exist($data);
        DayTimeModel::exist($data);
        $data['proj_id']=$proj_id;
        $data['scen_number'] = $scen_number;
        $data['scen_id']= self::exist($data);
        if($data['scen_id']==0) throw new Exception(419);
        if(array_key_exists('scen_number',$data)){
            if($scen_number != $data['scen_number']){
                if(!self::validNumberScene($data)) throw new Exception(421);
                self::updateNumberScene($data,$scen_number);
            }
        } 
        return self::updateMethod($data);
    }

    //DELETE
    static public function deleteScene($scen_number,$proj_id){
        $data['proj_id']=$proj_id;
        $data['scen_number'] = $scen_number;
        $data['scen_id']= self::exist($data);
        if($data['scen_id']==0) throw new Exception(419);
        self::deleteNumberScene($data);
        $query = 'DELETE FROM scenes WHERE scen_id = :scen_id';
        return self::executeQuery($query,404,$data);
    }
    

    //Extras
    static public function exist($data){
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        $query = '';
        if(array_key_exists('scen_id',$data)){
            $query = "SELECT scen_id FROM scenes WHERE scen_id = :scen_id";
        }
        else{
            $query = "SELECT scen_id FROM scenes WHERE proj_id=:proj_id AND scen_number = :scen_number";
        }
        $scen_id = self::executeQuery($query,1,$data,true);
        $scen_id = (isset($scen_id[1][0]['scen_id']))?$scen_id[1][0]['scen_id']:0;
        if($scen_id==0)throw new Exception(419);
        return $scen_id;
    }

    static public function getProjectId($data){
        $query = "SELECT proj_id FROM scenes WHERE scen_id = :scen_id";
        $proj_id = self::executeQuery($query,1,$data,true);
        $proj_id = (isset($proj_id[1][0]['proj_id']))?$proj_id[1][0]['proj_id']:0;
        return ($proj_id>0)?$proj_id:0;
    }

    static private function validNumberScene($data){
        $scenCount = (self::readProjectScenes($data['proj_id'])[1]->rowCount());
        return ($data['scen_number']<=$scenCount+1 && $data['scen_number']>0)?1:0;
    }

    static public function newNumberScene($data){
        $scenCount = (self::readProjectScenes($data['proj_id'])[1]->rowCount());
        if($data['scen_number']<=$scenCount){
            $query = 'SELECT scen_id,scen_number,proj_id FROM scenes WHERE scen_number>=:scen_number AND proj_id = :proj_id  ORDER BY scen_number ASC';
            //echo json_encode($data,JSON_UNESCAPED_SLASHES);
            self::numberChalenger($query,$data,true);
        }
    }
    
    static public function updateNumberScene($data,$preScen_id){
        if($data['scen_number']<$preScen_id){//Movimiento de mayor a menor
            $query = 'SELECT scen_id,scen_number,proj_id FROM scenes WHERE (scen_number BETWEEN :scen_number AND '.($preScen_id-1).') AND proj_id=:proj_id ORDER BY scen_number ASC';
            self::numberChalenger($query,$data,true);
        }
        if($data['scen_number']>$preScen_id){//Movimiento de menor a mayor
            $query = 'SELECT scen_id,scen_number,proj_id FROM scenes WHERE (scen_number BETWEEN '.($preScen_id+1).' AND :scen_id) AND proj_id=:proj_id  ORDER BY scen_number ASC';
            //echo json_encode($data,JSON_UNESCAPED_SLASHES);
            self::numberChalenger($query,$data,false);
        }
    }

    
    static public function deleteNumberScene($data){
        $scenCount = (self::readProjectScenes($data['proj_id'])[1]->rowCount());
        $query = 'SELECT scen_id,scen_number,proj_id FROM scenes WHERE scen_number>:scen_number AND proj_id = :proj_id  ORDER BY scen_number ASC';
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        $changeScenes = self::executeQuery($query,1,$data)[1]->fetchAll(PDO::FETCH_ASSOC);
        self::numberChalenger($query,$data,false);
    }

    static public function numberChalenger($query,$data,$way){
        $changeScenes = self::executeQuery($query,1,$data)[1]->fetchAll(PDO::FETCH_ASSOC);
        foreach($changeScenes as $scene){
            if($way){
                $scene['scen_number']++;
            }
            else{
                $scene['scen_number']--;
            }
            self::updateMethod($scene);
        }
    }

    
    static private function updateMethod($data) {
        if(!array_key_exists('scen_id',$data)){
            $data['scen_id'] = self::exist($data);
        }
        if($data['scen_id']!=0){
            ProjectModel::makeUpdate($data['proj_id']);
            $query = "UPDATE scenes SET ";
            $dataAO = new ArrayObject($data);
            $iter = $dataAO->getIterator();
            while($iter->valid()){
                if(is_numeric($iter->key())){ 
                    $iter->next();
                    continue;
                }
                $query .= $iter->key()."=:".$iter->key();
                $iter->next();
                if($iter->valid()){
                    $query .= ",";
                }
                else{
                    $query .= " WHERE scen_id =:scen_id";
                }
            }
            return self::executeQuery($query,403,$data);
        }
        return 419;
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            "scen_id",
            "scen_number",
            "scen_duration",
            "scen_place",
            "scen_argument",
            "dayT_id",
            "spac_id",
            "proj_id");
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":scen_id", $data["scen_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":scen_number", $data["scen_number"],PDO::PARAM_INT);
                        break;
                    case 2:
                        $statement->bindParam(":scen_duration", $data["scen_duration"],PDO::PARAM_INT);
                        break;
                    case 3:
                        $statement->bindParam(":scen_place", $data["scen_place"],PDO::PARAM_STR);
                        break;
                    case 4:
                        $statement->bindParam(":scen_argument", $data["scen_argument"],PDO::PARAM_STR);
                        break;
                    case 5:
                        $statement->bindParam(":dayT_id", $data["dayT_id"],PDO::PARAM_INT);
                        break;
                    case 6:
                        $statement->bindParam(":spac_id", $data["spac_id"],PDO::PARAM_INT);
                        break;
                    case 7:
                        $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
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