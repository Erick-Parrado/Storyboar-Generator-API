<?php
class ProjectModel{
    //POST
    static public function createProject($data){
        if(!self::projectExist($data)){
            $query = 'INSERT INTO projects(proj_tittle,proj_producer,proj_description,proj_pin,proj_dateUpdate) VALUES (:proj_tittle,:proj_producer,:proj_description,:proj_pin,:proj_dateUpdate)';
            return self::executeQuery($query,300,$data);
        }
        return 309;
    }
    
    //GET
    static public function readProject($id=null){
        $data = [];
        $query = 'SELECT proj_id,proj_tittle,proj_producer,proj_description,proj_dateUpdate FROM projects';
        if($id > 0 && $id != null){
            $data['proj_id'] = $id;
            $query .= ' WHERE proj_id =:proj_id';
        }
        return self::executeQuery($query,301,$data);
    }

    //PUT
    static public function updateProject($id,$data){

    }

    //Extras
    static public function projectExist($data){
        $query = "SELECT proj_id FROM projects WHERE proj_producer=:proj_producer AND proj_tittle=:proj_tittle";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }
    
    static public function pinExist($pin){
        $data['proj_pin'] = $pin;
        $query = "SELECT proj_pin FROM projects WHERE proj_pin=:proj_pin";
        $count = self::executeQuery($query,1,$data)[1]->rowCount();
        return ($count>0)?1:0;
    }

    //Ejecutor de queries
    static public function  executeQuery($query,$confirmCod = 0,$data=null,$fetch=false){
        $fields = array('proj_id','proj_tittle','proj_producer','proj_description','proj_dateUpdate','proj_pin');
        $statement= Connection::doConnection()->prepare($query);
        if(isset($data)){
            foreach(array_keys($fields) as $index){
                $pattern = '/^.*:'.$fields[$index].'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                switch($index){
                    case 0:
                        $statement->bindParam(":proj_id", $data["proj_id"],PDO::PARAM_INT);
                        break;
                    case 1:
                        $statement->bindParam(":proj_tittle", $data["proj_tittle"],PDO::PARAM_STR);
                        break;
                    case 2:
                        $statement->bindParam(":proj_producer", $data["proj_producer"],PDO::PARAM_STR);
                        break;
                    case 3:
                        $statement->bindParam(":proj_description", $data["proj_description"],PDO::PARAM_STR);
                        break;
                    case 4:
                        $statement->bindParam(":proj_dateUpdate", $data["proj_dateUpdate"],PDO::PARAM_STR);
                        break;
                    case 5:
                        $statement->bindParam(":proj_pin", $data["proj_pin"],PDO::PARAM_STR);
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
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            $statement-> closeCursor();
            $statement = null;
            if($error != false) return array(910,$error->getMessage());
            else return $confirmCod;
        }
    }
}
?>