<?php
require_once 'model/tableModel.php';

class RoleModel extends TableModel{
    public function __construct()
    {
        $table_name = 'roles';
        $table_prefix = 'role';
        $table_fields = array(
            'role_id',
            'role_name'
        );
        $model_baseCode = 1100;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }

    static public function readRoles($role_id){
        $data= [];
        $query = 'SELECT * FROM roles';
        if($role_id > 0 && $role_id != null){
            $data['role_id'] = $role_id;
            $query .= ' WHERE role_id =:role_id';
        }
        return self::executeQuery($query,751,$data);
    }

    static public function exist($data,$way=false){
        new RoleModel();
        parent::exist($data,$way);
    }

    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher =null){
        new RoleModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }

    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case 'role_id':
                    $statement->bindParam(":role_id", $data["role_id"],PDO::PARAM_INT);
                    break;
                case 'role_name':
                    $statement->bindParam(":role_name", $data["role_name"],PDO::PARAM_STR);
                    break;
            }
            return $statement;
        };
    }
}
?>