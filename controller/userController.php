<?php

class UserController{
    private $_method;
    private $_complement;
    private $_data;
    private $_add;

    const FIELDS = array('user_id','user_email','user_pass');

    function __construct($method, $complement=null, $data=null,$add=null){
        $this->_method = $method;
        $this->_complement = $complement == null ? 0: $complement;
        $this->_data = $data;
        $this->_add = $add;
    }

    public function index(){
        try{
            $this->validateData();
            switch ($this->_method){
                case 'GET':
                    $this->generateSalting();
                    break;
                default:
                    ResponseController::response(104);
            }
        }
        catch(Exception $e){
            ResponseController::response((int)$e->getMessage());
        }
    }

    private function validateFields(){$dataAO = new ArrayObject($this->_data);
        $iter = $dataAO -> getIterator();
        while($iter->valid()){
            if(!in_array($iter->key(),self::FIELDS)){
                throw new Exception(220);
            }
            $iter->next();
        }
    }

    private function validateValues(){
        $patterns = array(
            "user_email"=>"/^[a-zA-Z0-9_.]{8,}@gmail.com$/",
            "user_pass"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/",
            "user_phone"=>"/^[0-9]{7}$/"
        );
        if(is_array($this->_data)){
            $dataAO = new ArrayObject($this->_data);
            $iter = $dataAO -> getIterator();
            $index = 1;
            while($iter->valid()){
                $pattern = (array_key_exists($iter->key(),$patterns))?$patterns[$iter->key()]:null;
                if(isset($pattern)){
                    $result = preg_match($pattern,$iter->current());
                    if(!$result) {
                        throw new Exception(220+$index);
                    };
                }
                $index++;
                $iter->next();
            }
        }
    }

    private function validateData(){
        try{
            $this->validateFields();
            $this->validateValues();
        }
        catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }


    private function generateSalting(){
        $trimmed_data="";
        if(($this->_data !="") || (!empty($this->_data))){
            $trimmed_data = array_map('trim', $this->_data);
            if(isset($this->_data['user_pass'])){
                $key = crypt($trimmed_data['user_pass'],'$1$story$board$Generator$');
                $key = str_replace("$","ERT",$key);
                $trimmed_data['us_key']=$key;
            }
            if(isset($this->_data['user_email'])){
                $identifier = crypt($trimmed_data['user_email'],'$1$Wasaaaa$');
                $identifier = str_replace("$","y78",$identifier);
                $trimmed_data['us_identifier']=$identifier;
            }
            echo json_encode($trimmed_data);
            return $trimmed_data;
        }
        return $this->_data;
    }
}

?>