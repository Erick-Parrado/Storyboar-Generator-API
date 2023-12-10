<?php
class ShotModel extends TableModel{
    public function __construct()
    {
        $table_name = 'shots';
        $table_prefix = 'shot';
        $table_fields = array(
            'shot_id',
            'shot_name'
        );
        $model_baseCode = 1600;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    static public function readShots($spac_id){
        $data= [];
        $query = 'SELECT * FROM shots';
        if($spac_id > 0 && $spac_id != null){
            $data['shot_id'] = $spac_id;
            $query .= ' WHERE shot_id=:shot_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data,$way=false){
        new ShotModel();
        parent::exist($data,$way);
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new ShotModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }
    
    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "shot_id":
                    $statement->bindParam(":shot_id", $data["shot_id"],PDO::PARAM_INT);
                    break;
                case "shot_name":
                    $statement->bindParam(":shot_name", $data["shot_name"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>