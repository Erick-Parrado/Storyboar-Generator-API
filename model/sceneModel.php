<?php

require_once 'model/projectModel.php';
require_once 'model/spaceModel.php';
require_once 'model/dayTimeModel.php';
require_once 'model/tableModel.php';

class SceneModel extends TableModel{
    public function __construct()
    {
        $table_name = 'scenes';
        $table_prefix = 'scen';
        $table_fields = array(
            "scen_id",
            "scen_number",
            "scen_duration",
            "scen_place",
            "scen_argument",
            "dayT_id",
            "spac_id",
            "proj_id"
        );
        $model_baseCode = 400;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    //GET
    static public function readScene($scen_number,$proj_id){
        $data['scen_number'] = $scen_number;
        $data['proj_id'] = $proj_id;
        $data = self::exist($data);
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
        $data['scen_place'] = strtoupper($data['scen_place']);
        SpaceModel::exist($data);
        DayTimeModel::exist($data);
        self::validNumberScene($data);
        self::newNumberScene($data); 
        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
        $query = 'INSERT INTO scenes(scen_number, scen_duration, scen_place, dayT_id, spac_id, scen_argument,proj_id) VALUES  (:scen_number,:scen_duration,:scen_place,:dayT_id,:spac_id,:scen_argument,:proj_id)';
        return self::executeQuery($query,400,$data);
    }

    //PUT
    static public function updateScene($scen_number,$proj_id,$data){
        SpaceModel::exist($data);
        DayTimeModel::exist($data);
        $data['proj_id']=$proj_id;
        $posScen_number = $data['scen_number'];
        $data['scen_number']= $scen_number;
        $data= self::exist($data);
        self::exist($data);
        if(array_key_exists('scen_number',$data)){
            if($scen_number != $posScen_number){
                $data['scen_number']= $posScen_number;
                self::validNumberScene($data);
                self::updateNumberScene($data,$scen_number);
            }
        } 
        self::updateMethod($data);
        return 403;
    }

    //DELETE
    static public function deleteScene($scen_number,$proj_id){
        $data['proj_id']=$proj_id;
        $data['scen_number'] = $scen_number;
        $data = self::exist($data);
        //self::exist($data);
        
        self::deleteNumberScene($data);
        $query = 'DELETE FROM scenes WHERE scen_id = :scen_id';
        return self::executeQuery($query,404,$data);
    }
    

    //Extras
    static public function exist($data,$way=false){
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        if(array_key_exists('scen_id',$data)){
            new SceneModel();
            parent::exist($data,$way);
        }
        else{
            $query = "SELECT scen_id FROM scenes WHERE proj_id=:proj_id AND scen_number = :scen_number";
            $statement = self::executeQuery($query,1,$data,true);
            $data['scen_id'] = (isset($statement[1][0]['scen_id']))?$statement[1][0]['scen_id']:0;
            if($data['scen_id']==0)throw new Exception(419);
            return $data;
        }
    }

    static public function getProjectId($data){
        $query = "SELECT proj_id FROM scenes WHERE scen_id = :scen_id";
        $proj_id = self::executeQuery($query,1,$data,true);
        $proj_id = (isset($proj_id[1][0]['proj_id']))?$proj_id[1][0]['proj_id']:0;
        return ($proj_id>0)?$proj_id:0;
    }

    static private function validNumberScene($data){
        $scenCount = (self::readProjectScenes($data['proj_id'])[1]->rowCount());
        if($data['scen_number']>$scenCount+1 || $data['scen_number']<0) throw new Exception(421);
    }

    static public function newNumberScene($data){
        $scenCount = (self::readProjectScenes($data['proj_id'])[1]->rowCount());
        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
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
            $query = 'SELECT scen_id,scen_number,proj_id FROM scenes WHERE (scen_number BETWEEN '.($preScen_id+1).' AND :scen_number) AND proj_id=:proj_id  ORDER BY scen_number ASC';

            self::numberChalenger($query,$data,false);
        }
    }

    
    static public function deleteNumberScene($data){
        $query = 'SELECT scen_id,scen_number,proj_id FROM scenes WHERE scen_number>:scen_number AND proj_id = :proj_id  ORDER BY scen_number ASC';
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        self::numberChalenger($query,$data,false);
    }

    static public function numberChalenger($query,$data,$way){
        $changeScenes = self::executeQuery($query,1,$data)[1]->fetchAll(PDO::FETCH_ASSOC);
        //echo json_encode($changeScenes,JSON_UNESCAPED_SLASHES);
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
    
    static protected function updateMethod($data) {
        if(!array_key_exists('scen_id',$data)){
            $data['scen_id'] = self::exist($data);
        }
        self::exist($data);
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        ProjectModel::makeUpdate($data['proj_id']);
        new SceneModel();
        parent::updateMethod($data);
    }
    
    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new SceneModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }

    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "scen_id":
                    $statement->bindParam(":scen_id", $data["scen_id"],PDO::PARAM_INT);
                    break;
                case "scen_number":
                    $statement->bindParam(":scen_number", $data["scen_number"],PDO::PARAM_INT);
                    break;
                case "scen_duration":
                    $statement->bindParam(":scen_duration", $data["scen_duration"],PDO::PARAM_INT);
                    break;
                case "scen_place":
                    $statement->bindParam(":scen_place", $data["scen_place"],PDO::PARAM_STR);
                    break;
                case "scen_argument":
                    $statement->bindParam(":scen_argument", $data["scen_argument"],PDO::PARAM_STR);
                    break;
                case "dayT_id":
                    $statement->bindParam(":dayT_id", $data["dayT_id"],PDO::PARAM_INT);
                    break;
                case "spac_id":
                    $statement->bindParam(":spac_id", $data["spac_id"],PDO::PARAM_INT);
                    break;
                case "proj_id":
                    $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
                    break;
            }
            return $statement;
        };
    }
}
?>