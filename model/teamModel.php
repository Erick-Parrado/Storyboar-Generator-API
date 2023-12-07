<?php
require_once 'model/projectModel.php';
require_once 'model/userModel.php';

class TeamModel{
    //POST
    static public function accessProject($id,$pin){
        $pinProject =ProjectModel::projectByPIN($pin);
        $data['user_id'] = $id;
        $data['role_id'] = 3;
        if($pinProject == false){
            return 319;
        }
        if(!UserModel::idExist($data)){
            return 219;
        }
        $data['proj_id'] = $pinProject;
        $data['team_id'] = $pinProject.''.$id;
        if(self::accessMade($data)){
            return 709;
        }
        $query = 'INSERT INTO team_members(team_id,proj_id,user_id,role_id) VALUES (:team_id,:proj_id,:user_id,:role_id)';
        return self::executeQuery($query,700,$data);
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