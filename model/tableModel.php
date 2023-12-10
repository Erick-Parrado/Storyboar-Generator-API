<?php
class TableModel{
    static public $_table;
    static public $_prefix;
    static public $_fields;
    static public $_baseCode;
    static public $_matcher;

    public function __construct($table,$prefix,$fields,$matcher,$baseCode)
    {
        self::$_table = $table;
        self::$_prefix = $prefix;
        self::$_fields = ($fields==null)?[]:$fields;
        self::$_matcher = $matcher;
        self::$_baseCode = $baseCode;
    }

    static public function cleanData($data){
        $dataAO = new ArrayObject($data);
        $iter = $dataAO->getIterator();
        while($iter->valid()){
            if(!in_array($iter->key(),self::$_fields)) unset($data[$iter->key()]);
            $iter->next();
        }
        return $data;
    }
    
    static public function exist($data,$way=false){
        $query = "SELECT ".self::$_prefix."_id FROM ".self::$_table." WHERE ".self::$_prefix."_id =:".self::$_prefix."_id";
        $count = self::executeQuery($query,200,$data)[1]->rowCount();
        if($way){
            if($count>0) throw new Exception(self::$_baseCode+9);
        }
        else{
            if($count<=0) throw new Exception(self::$_baseCode+19);
        }
    }

    static protected function updateMethod($data){
        self::cleanData($data);
        $query = "UPDATE ".self::$_table." SET ";
        $dataAO = new ArrayObject($data);
        $iter = $dataAO->getIterator();
        while($iter->valid()){
            $query .= $iter->key()."=:".$iter->key();
            $iter->next();
            if($iter->valid()){
                $query .= ",";
            }
            else{
                $query .= " WHERE ".self::$_prefix."_id =:".self::$_prefix."_id";
            }
        }
        self::executeQuery($query,1,$data,self::$_matcher);
    }

    static public function  executeQuery($query,$confirmCod,$data=null,$fetch=false,$matcher=null){
        $matcher = ($matcher == null)?self::$_matcher:$matcher;
        $statement= Connection::doConnection()->prepare($query);
        //echo json_encode(self::$_fields,JSON_UNESCAPED_UNICODE);
        //echo json_encode($data,JSON_UNESCAPED_UNICODE);
        if(isset($data)){
            foreach(self::$_fields as $field){
                $pattern = '/^.*:'.$field.'.*$/';
                $result = (preg_match($pattern,$query));
                if(!$result) continue;
                $statement = $matcher($statement,$field,$data);
            }
        }

        if(preg_match('/^SELECT.*$/',$query)){
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            if($error != false) throw new Exception($error->getMessage());//return array(910,$error->getMessage());
            if($fetch) return array($confirmCod,$statement->fetchAll());
            return array($confirmCod,$statement);
        }
        else{
            $error = $statement->execute() ? false : Connection::doConnection()->errorInfo();
            $statement-> closeCursor();
            $statement = null;
            if($error != false) throw new Exception($error->getMessage());//return array(910,$error->getMessage());
            else return $confirmCod;
        }
    }
}
?>