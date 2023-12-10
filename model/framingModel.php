<?php
require_once 'model/tableModel.php';

class FramingModel extends TableModel{
    public function __construct()
    {
        $table_name = 'framings';
        $table_prefix = 'fram';
        $table_fields = array(
            'fram_id',
            'fram_name',
            'fram_abr'
        );
        $model_baseCode = 1400;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    static public function readFramings($fram_id){
        $data= [];
        $query = 'SELECT * FROM  framings';
        if($fram_id > 0 && $fram_id != null){
            $data['fram_id'] = $fram_id;
            $query .= ' WHERE fram_id =:fram_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data,$way=false){
        new FramingModel();
        parent::exist($data,$way);
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new FramingModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }
    
    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "fram_id":
                    $statement->bindParam(":fram_id", $data["fram_id"],PDO::PARAM_INT);
                    break;
                case "fram_name":
                    $statement->bindParam(":fram_name", $data["fram_name"],PDO::PARAM_STR);
                    break;
                case "fram_abr":
                    $statement->bindParam(":fram_abr", $data["fram_standard"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>