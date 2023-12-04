<?php

require_once 'model/userModel.php';

class UserController{
    private $_method;
    private $_complement;
    private $_data;
    private $_add;

    const FIELDS = array('user_id','user_name','user_lastName','user_email','user_pass','user_phone','user_age','us_identifier','us_key');

    function __construct($method, $complement=null, $data=null,$add=null){
        $this->_method = $method;
        $this->_complement = $complement == null ? 0: $complement;
        $this->_data = $data;
        $this->_add = $add;
    }

    public function index(){
        try{
            switch ($this->_method){
                case 'GET':
                    echo 'Getting';
                    break;
                case 'POST':
                    $this->validateData();
                    $this->generateSalting();
                    echo UserModel::createUser($this->_data);
                    //ResponseController::response(UserModel::createUser($this->_data));
                    break;
                default:
                    ResponseController::response(104);
            }
        }
        catch(Exception $e){
            echo $e->getMessage();
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
            "user_phone"=>"/^[0-9]{10}$/"
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
                    $index++;
                }
                $iter->next();
            }
        }
    }

    private function existData(){
        if($this->_data == null) throw new Exception(299);
    }

    private function validateData(){
        try{
            $this->existData();
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
            $this->_data=$trimmed_data;
            return;
        }
    }
}

?>