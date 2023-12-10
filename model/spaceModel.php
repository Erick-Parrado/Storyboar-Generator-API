<?php
require_once 'model/tableModel.php';

class SpaceModel extends TableModel{
    public function __construct()
    {
        $table_name = 'spaces';
        $table_prefix = 'spac';
        $table_fields = array(
            'spac_id',
            'spac_name',
            'spac_abr'
        );
        $model_baseCode = 1300;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    static public function readSpaces($spac_id){
        $data= [];
        $query = 'SELECT * FROM spaces';
        if($spac_id > 0 && $spac_id != null){
            $data['spac_id'] = $spac_id;
            $query .= ' WHERE spac_id=:spac_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data,$way=false){
        new SpaceModel();
        parent::exist($data,$way);
    }
    
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new SpaceModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }

    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "spac_id":
                    $statement->bindParam(":spac_id", $data["spac_id"],PDO::PARAM_INT);
                    break;
                case "spac_name":
                    $statement->bindParam(":spac_name", $data["spac_name"],PDO::PARAM_STR);
                    break;
                case "spac_abr":
                    $statement->bindParam(":spac_abr", $data["spac_standard"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>