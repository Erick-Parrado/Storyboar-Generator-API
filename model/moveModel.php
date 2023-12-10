<?php
require_once 'model/tableModel.php';

class MoveModel extends TableModel{
    public function __construct()
    {
        $table_name = 'moves';
        $table_prefix = 'move';
        $table_fields = array(
            'move_id',
            'move_name'
        );
        $model_baseCode = 1500;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }
    
    static public function readMoves($move_id){
        $data= [];
        $query = 'SELECT * FROM moves';
        if($move_id > 0 && $move_id != null){
            $data['move_id'] = $move_id;
            $query .= ' WHERE move_id =:move_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data,$way=false){
        new MoveModel();
        parent::exist($data,$way);
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new MoveModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }
    
    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "move_id":
                    $statement->bindParam(":move_id", $data["move_id"],PDO::PARAM_INT);
                    break;
                case "move_name":
                    $statement->bindParam(":move_name", $data["move_name"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>