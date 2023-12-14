<?php

require_once 'model/sceneModel.php';
require_once 'model/shotModel.php';
require_once 'model/framingModel.php';
require_once 'model/moveModel.php';
require_once 'model/tableModel.php';

class PlaneModel extends TableModel{
    public function __construct()
    {
        $table_name = 'planes';
        $table_prefix = 'plan';
        $table_fields = array(
            "plan_id",
            "plan_number",
            "plan_duration",
            "plan_description",
            "plan_image",
            "shot_id",
            "move_id",
            "fram_id",
            "scen_id"
        );
        $model_baseCode = 600;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    //GET
    static public function readPlane($plan_number,$scen_number,$proj_id){
        if($proj_id == null) throw new Exception(428);
        if($scen_number == null) throw new Exception(528);
        $data['plan_number'] = $plan_number;
        $data['scen_number'] = $scen_number;
        $data['proj_id'] = $proj_id;
        $data = SceneModel::exist($data);
        $query = 'SELECT * FROM planes ';
        if($plan_number > 0 && $plan_number != null){
            $data = self::exist($data);
            $query .= ' WHERE plan_id =:plan_id';
        }
        $query .= ' ORDER BY plan_number ASC';
        //echo $query;
        
        return self::executeQuery($query,501,$data);
    }

    //POST
    static public function createPlane($scen_number,$proj_id,$data){
        $data['proj_id'] = $proj_id;
        $data['scen_number'] = $scen_number;
        ShotModel::exist($data);
        FramingModel::exist($data);
        MoveModel::exist($data);
        ProjectModel::makeUpdate($proj_id);
        $data = SceneModel::exist($data);
        self::validNumberPlane($data);
        self::newNumberPlane($data); 
        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
        $query = 'INSERT INTO planes(plan_duration, plan_description, plan_image, shot_id, fram_id, move_id, scen_id, plan_number) VALUES  (:plan_duration,:plan_description,:plan_image,:shot_id,:fram_id,:move_id,:scen_id,:plan_number)';
        
        return self::executeQuery($query,500,$data);
    }

    //PUT
    static public function updatePlane($plan_number,$scen_number,$proj_id,$data){
        $data['proj_id']=$proj_id;
        $data['scen_number'] = $scen_number;
        $posPlan_number = $data['plan_number'];
        $data['plan_number'] = $plan_number;
        $data = SceneModel::exist($data);
        MoveModel::exist($data);
        FramingModel::exist($data);
        ShotModel::exist($data);
        $data = self::exist($data);
        if($data['plan_id']==0) throw new Exception(519);
        if(array_key_exists('plan_number',$data)){
            if($plan_number != $posPlan_number){
                $data['plan_number'] = $posPlan_number;
                self::validNumberPlane($data);
                self::updateNumberPlane($data,$plan_number);
            }
        } 
        return self::updateMethod($data);
    }

    //DELETE
    static public function deletePlane($plan_number,$scen_number,$proj_id){
        $data['proj_id']=$proj_id;
        $data['scen_number'] = $scen_number;
        $data['plan_number'] = $plan_number;
        $data =SceneModel::exist($data);
        ProjectModel::makeUpdate($proj_id);
        $data = self::exist($data);
        self::deleteNumberPlane($data);
        $query = 'DELETE FROM planes WHERE plan_id = :plan_id';
        return self::executeQuery($query,504,$data);
    }
    

    //Extras
    static public function exist($data,$way=false){
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        new PlaneModel();
        parent::exist($data,$way);
        if(!array_key_exists('scen_id',$data)){
            $data= SceneModel::exist($data);
        }
        $query = "SELECT plan_id FROM planes WHERE plan_number=:plan_number AND scen_id = :scen_id";
        $statement = self::executeQuery($query,1,$data,true);
        $data['plan_id'] = (isset($statement[1][0]['plan_id']))?$statement[1][0]['plan_id']:0;
        if($data['plan_id']==0)throw new Exception(519);
        return $data;
    }

    static private function validNumberPlane($data){
        $planeCount = (self::readPlane(0,$data['scen_number'],$data['proj_id'])[1]->rowCount());
        if($data['plan_number']>$planeCount+1 || $data['plan_number']==0)throw new Exception(521);
    }

    static public function newNumberPlane($data){
        self::validNumberPlane($data);
        $query = 'SELECT plan_id,plan_number,scen_id FROM planes WHERE plan_number>=:plan_number AND scen_id = :scen_id  ORDER BY plan_number ASC';
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        self::numberChalenger($query,$data,true);
    
    }
    
    static public function updateNumberPlane($data,$prePlan_id){
        if($data['plan_number']<$prePlan_id){//Movimiento de mayor a menor
            $query = 'SELECT plan_id,plan_number,scen_id FROM planes WHERE (plan_number BETWEEN :plan_number AND '.($prePlan_id-1).') AND scen_id=:scen_id ORDER BY plan_number ASC';
            self::numberChalenger($query,$data,true);
        }
        if($data['plan_number']>$prePlan_id){//Movimiento de menor a mayor
            $query = 'SELECT plan_id,plan_number,scen_id FROM planes WHERE (plan_number BETWEEN '.($prePlan_id+1).' AND :plan_number) AND scen_id=:scen_id  ORDER BY plan_number ASC';
            //echo json_encode($data,JSON_UNESCAPED_SLASHES);
            self::numberChalenger($query,$data,false);
        }
    }

    
    static public function deleteNumberPlane($data){
        $query = 'SELECT plan_id,plan_number,scen_id FROM planes WHERE plan_number>:plan_number AND scen_id = :scen_id  ORDER BY plan_number ASC';
        //echo json_encode($data,JSON_UNESCAPED_SLASHES);
        self::numberChalenger($query,$data,false);
    }

    static public function numberChalenger($query,$data,$way){
        $changePlanes = self::executeQuery($query,1,$data)[1]->fetchAll(PDO::FETCH_ASSOC);
        foreach($changePlanes as $plane){
            if($way){
                $plane['plan_number']++;
            }
            else{
                $plane['plan_number']--;
            }
            self::updateMethod($plane);
        }
    }

    
    static protected function updateMethod($data) {
        if(!array_key_exists('plan_id',$data)){
            $data['plan_id'] = self::exist($data);
        }
        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
        SceneModel::exist($data);
        $proj_id = SceneModel::getProjectId($data);
        if($data['plan_id']!=0){
            ProjectModel::makeUpdate($proj_id);
            SceneModel::exist($data);
            new PlaneModel();
            parent::updateMethod($data);
            return 503;
        }
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new PlaneModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }

    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "plan_id":
                    $statement->bindParam(":plan_id", $data["plan_id"],PDO::PARAM_INT);
                    break;
                case "plan_number":
                    $statement->bindParam(":plan_number", $data["plan_number"],PDO::PARAM_INT);
                    break;
                case "plan_duration":
                    $statement->bindParam(":plan_duration", $data["plan_duration"],PDO::PARAM_INT);
                    break;
                case "plan_description":
                    $statement->bindParam(":plan_description", $data["plan_description"],PDO::PARAM_STR);
                    break;
                case "plan_image":
                    $statement->bindParam(":plan_image", $data["plan_image"],PDO::PARAM_STR);
                    break;
                case "shot_id":
                    $statement->bindParam(":shot_id", $data["shot_id"],PDO::PARAM_INT);
                    break;
                case "move_id":
                    $statement->bindParam(":move_id", $data["move_id"],PDO::PARAM_INT);
                    break;
                case "fram_id":
                    $statement->bindParam(":fram_id", $data["fram_id"],PDO::PARAM_INT);
                    break;
                case "scen_id":
                    $statement->bindParam(":scen_id", $data["scen_id"],PDO::PARAM_INT);
                    break;
            }
            return $statement;
        };
    }

    
}
?>