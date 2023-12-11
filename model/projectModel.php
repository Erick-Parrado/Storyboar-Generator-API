<?php
require_once 'model/tableModel.php';
require_once 'model/teamModel.php';

class ProjectModel extends TableModel{
    public function __construct()
    {
        $table_name = 'projects';
        $table_prefix = 'proj';
        $table_fields = array(
            'proj_id',
            'proj_tittle',
            'proj_producer',
            'proj_description',
            'proj_dateUpdate',
            'proj_pin'
        );
        
        $model_baseCode = 300;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    //POST
    static public function createProject($data,$user_id){
        self::projectExist($data);
        $data['proj_pin'] = self::generatePIN();
        $data['proj_dateUpdate'] = self::makeUpdate();
        $query = 'INSERT INTO projects(proj_tittle,proj_producer,proj_description,proj_pin,proj_dateUpdate) VALUES (:proj_tittle,:proj_producer,:proj_description,:proj_pin,:proj_dateUpdate)';
        $response = self::executeQuery($query,300,$data);
        $data['user_id'] = $user_id;
        TeamModel::accessProject($data,1);
        return $response;
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
    static public function updateProject($proj_id,$data){
        $data['proj_id'] = $proj_id;
        self::exist($data);
        $data['proj_dateUpdate'] = self::makeUpdate();
        parent::updateMethod($data);
        return 302;
    }

    //DELETE
    static public function deleteProject($proj_id){
        $data['proj_id'] = $proj_id;
        self::exist($data);
        $query = "DELETE FROM projects WHERE proj_id = :proj_id";
        return self::executeQuery($query,303,$data);
    }
    //Extras
    static public function exist($data,$way=false){
        new ProjectModel();
        parent::exist($data,$way);
    }

    static public function projectExist($data){
        $query = "SELECT proj_id FROM projects WHERE proj_producer=:proj_producer AND proj_tittle=:proj_tittle";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        if($count>0)throw new Exception(309);
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

    static public function projectByPIN($data){
        if(self::pinExist($data)){
            $query = 'SELECT proj_id FROM projects WHERE proj_pin=:proj_pin';
            $proj_id = self::executeQuery($query,1,$data,true)[1][0]['proj_id'];
            return $proj_id;
        }
        throw new Exception(319);
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
    }

    static public function makeUpdate($proj_id = null){
        $data['proj_dateUpdate'] = date('d/m/Y h:i:s', time());
        if($proj_id==null){
            return $data['proj_dateUpdate'];
        }
        $result = self::updateProject($proj_id,$data);
    }
    
    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new ProjectModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }
    
    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "proj_id":
                    $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
                    break;
                case "proj_tittle":
                    $statement->bindParam(":proj_tittle", $data["proj_tittle"],PDO::PARAM_STR);
                    break;
                case "proj_producer":
                    $statement->bindParam(":proj_producer", $data["proj_producer"],PDO::PARAM_STR);
                    break;
                case "proj_description":
                    $statement->bindParam(":proj_description", $data["proj_description"],PDO::PARAM_STR);
                    break;
                case "proj_dateUpdate":
                    $statement->bindParam(":proj_dateUpdate", $data["proj_dateUpdate"],PDO::PARAM_STR);
                    break;
                case "proj_pin":
                    $statement->bindParam(":proj_pin", $data["proj_pin"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>