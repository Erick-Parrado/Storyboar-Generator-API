<?php
class TableModel{
    static public $_table;
    static public $_prefix;
    static public $_fields;
    static public $_baseCode;

    public function __construct($table,$prefix,$fields,$baseCode)
    {
        self::$_table = $table;
        self::$_prefix = $prefix;
        self::$_fields = ($fields==null)?[]:$fields;
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

    static public function  executeQuery($query,$confirmCod,$data,$fetch,$matcher){
        $statement= Connection::doConnection()->prepare($query);
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