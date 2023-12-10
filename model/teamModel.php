<?php
require_once 'model/projectModel.php';
require_once 'model/userModel.php';
require_once 'model/roleModel.php';
require_once 'model/tableModel.php';

class TeamModel extends TableModel{
    public function __construct()
    {
        $table_name = 'team_members';
        $table_prefix = 'team';
        $table_fields = array(
            'team_id',
            'proj_id',
            'user_id',
            'role_id'
        );
        $model_baseCode = 700;
        $matcher = self::getMatcher();
        parent::__construct($table_name,$table_prefix,$table_fields,$matcher,$model_baseCode);
    }
    //GET
    static public function readUsers($proj_id){
        $data['proj_id'] = $proj_id;
        ProjectModel::exist($data);
        $query = 'SELECT team_id,proj_id,users.user_id,users.user_name,users.user_lastName,users.user_email,users.user_phone FROM team_members INNER JOIN users ON team_members.user_id = users.user_id WHERE team_members.proj_id = :proj_id';
        return self::executeQuery($query,701,$data);
    }
    
    static public function readProjects($user_id){
        $data['user_id'] = $user_id;
        UserModel::exist($data);
        $query = 'SELECT team_id,team_members.proj_id,proj_tittle,proj_producer,proj_description FROM team_members INNER JOIN projects ON team_members.proj_id = projects.proj_id WHERE team_members.user_id=:user_id';
        return self::executeQuery($query,702,$data);
    }

    //POST
    static public function accessProject($data){
        $data['proj_id'] = ProjectModel::projectByPIN($data);
        $data['role_id'] = 3;
        UserModel::exist($data);
        $data['team_id'] = $data['proj_id'].''.$data['user_id'];
        self::accessMade($data,true);
        $query = 'INSERT INTO team_members(team_id,proj_id,user_id,role_id) VALUES (:team_id,:proj_id,:user_id,:role_id)';
        return self::executeQuery($query,700,$data);
    }

    //PUT
    static public function updateRole($data){
        $data['team_id'] = $data['proj_id'].''.$data['user_id'];
        self::accessMade($data,false);
        RoleModel::exist($data);
        $query = 'UPDATE team_members SET role_id =:role_id WHERE team_id = :team_id';
        return self::executeQuery($query,703,$data);
    }

    //DELETE
    static public function deleteMember($data){
        $data['team_id'] = $data['proj_id'].''.$data['user_id'];
        self::accessMade($data,false);
        $query = 'DELETE FROM team_members WHERE team_id = :team_id';
        return self::executeQuery($query,704,$data);
    }

    //Extras
    static private function accessMade($data,$way){
        new TeamModel();
        parent::exist($data,$way);
    }
    
    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false,$matcher=null){
        new TeamModel();
        return parent::executeQuery($query,$confirmCod,$data,$fetch);
    }

    static private function getMatcher(){
        return function($statement,$field,$data){
            switch($field){
                case "team_id":
                    $statement->bindParam(":team_id", $data["team_id"],PDO::PARAM_INT);
                    break;
                case "proj_id":
                    $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
                    break;
                case "user_id":
                    $statement->bindParam(":user_id", $data["user_id"],PDO::PARAM_INT);
                    break;
                case "role_id":
                    $statement->bindParam(":role_id", $data["role_id"],PDO::PARAM_INT);
                    break;
            }
            return $statement;
        };
    }
}
?>