<?php
require_once 'model/projectModel.php';
require_once 'model/userModel.php';
require_once 'model/roleModel.php';

class TeamModel{
    //GET
    static public function readUsers($proj_id){
        $data['proj_id'] = $proj_id;
        if(ProjectModel::idExist($data)){
            $query = 'SELECT team_id,proj_id,users.user_id,users.user_name,users.user_lastName,users.user_email,users.user_phone FROM team_members INNER JOIN users ON team_members.user_id = users.user_id WHERE team_members.proj_id = :proj_id';
            return self::executeQuery($query,701,$data);
        }
        return 319;
    }
    
    static public function readProjects($user_id){
        $data['user_id'] = $user_id;
        if(UserModel::idExist($data)){
            $query = 'SELECT team_id,team_members.proj_id,proj_tittle,proj_producer,proj_description FROM team_members INNER JOIN projects ON team_members.proj_id = projects.proj_id WHERE team_members.user_id = :user_id';
            return self::executeQuery($query,702,$data);
        }
        return 219;
    }

    //POST
    static public function accessProject($data){
        $pinProject =ProjectModel::projectByPIN($data);
        $data['role_id'] = 3;
        if($pinProject == false){
            return 319;
        }
        if(!UserModel::idExist($data)){
            return 219;
        }
        $data['proj_id'] = $pinProject;
        $data['team_id'] = $data['proj_id'].''.$data['user_id'];
        if(self::accessMade($data)){
            return 709;
        }
        $query = 'INSERT INTO team_members(team_id,proj_id,user_id,role_id) VALUES (:team_id,:proj_id,:user_id,:role_id)';
        return self::executeQuery($query,700,$data);
    }

    //PUT
    static public function updateRole($team_id,$role_id){
        $data = $role_id;
        $data['team_id'] = $team_id;
        if(!self::accessMade($data)) return 710;
        if(!RoleModel::idExist($data)) return 759;
        $query = 'UPDATE team_members SET role_id =:role_id WHERE team_id = :team_id';

        return self::executeQuery($query,703,$data);
    }

    //Extras
    static private function accessMade($data){
        $query = 'SELECT team_id FROM team_members WHERE team_id =:team_id';
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    static public function executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array(
            'team_id',
            'proj_id',
            'user_id',
            'role_id'
        );
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":team_id", $data["team_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
                        break;
                    case 2:
                        $statement->bindParam(":user_id", $data["user_id"],PDO::PARAM_INT);
                        break;
                    case 3:
                        $statement->bindParam(":role_id", $data["role_id"],PDO::PARAM_INT);
                        break;
                }
            }
        }

        if(preg_match('/^SELECT.*$/',$query)){
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            if($error != false) return array(910,$error->getMessage());
            if($fetch) return array($confirmCod,$statement->fetchAll());
            return array($confirmCod,$statement);
        }
        else{
            try{
                $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
                $statement-> closeCursor();
                $statement = null;
                if($error != false) return array(910,$error->getMessage());
                else return $confirmCod;
            }
            catch(Exception $e){
                echo ($e->getMessage());
            }
        }
    }
}
?>