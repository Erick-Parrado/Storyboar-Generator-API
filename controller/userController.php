<?php

require_once 'model/userModel.php';
require_once 'controller/endpointController.php';

class UserController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array('user_id','user_name','user_lastName','user_email','user_pass','user_phone','us_identifier','us_key');
        parent::__construct(200,$method,$complement,$data,$add,$fields);
    }

    public function index(){
        try{
            $response = null;
            switch ($this->_method){
                case 'GET':
                    $response = UserModel::readUser($this->_complement);
                    break;
                case 'POST':
                    $this->existData();
                    $this->strictFields();
                    $this->validateFields();
                    $this->validateValues();
                    $this->generateSalting();
                    $response = UserModel::createUser($this->_data);
                    break;
                default:
                    $response = 104;
            }
            ResponseController::response($response);
        }
        catch(Exception $e){
            ResponseController::response((int)$e->getMessage(),$e->getMessage());
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