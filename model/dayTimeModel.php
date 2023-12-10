<?php
require_once 'model/tableModel.php';

class DayTimeModel extends TableModel{
    public function __construct()
    {
        $table_name = 'day_times';
        $table_prefix = 'dayT';
        $table_fields = array(
            'dayT_id',
            'dayT_name',
            'dayT_standard'
        );
        $model_baseCode = 1200;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }
    
    static public function readDayTimes($dayT_id){
        $data= [];
        $query = 'SELECT * FROM day_times';
        if($dayT_id > 0 && $dayT_id != null){
            $data['dayT_id'] = $dayT_id;
            $query .= ' WHERE dayT_id =:dayT_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data,$way=false){
        new DayTimeModel();
        parent::exist($data,$way);
    }

    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new DayTimeModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }

    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "dayT_id":
                    $statement->bindParam(":dayT_id", $data["dayT_id"],PDO::PARAM_INT);
                    break;
                case "dayT_name":
                    $statement->bindParam(":dayT_name", $data["dayT_name"],PDO::PARAM_STR);
                    break;
                case "dayT_standard":
                    $statement->bindParam(":dayT_standard", $data["dayT_standard"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>