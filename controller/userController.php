<?php

require_once 'model/userModel.php';
require_once 'controller/endpointController.php';

class UserController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(
            'user_id',
            'user_name',
            'user_lastName',            
            "user_email"=>"/^([a-zA-Z0-9_.]{8,})@([a-z]{5,})\.([a-z]{2,3})(\.[a-z]{2,3})?$/",
            "user_pass"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/",
            "user_phone"=>"/^[0-9]{10}$/",
            'us_identifier',
            'us_key'
        );
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
                    $strictFields = array('user_name','user_lastName','user_email','user_pass','user_phone');
                    $this->setStrict($strictFields);
                    $this->validateValues();
                    $this->generateSalting();
                    //var_dump($this->_data);
                    $response = UserModel::createUser($this->_data);
                    break;
                case 'PUT':
                    $this->validateValues();
                    $this->generateSalting();
                    $response = UserModel::updateUser($this->_complement,$this->_data);
                    break;
                case 'DELETE':
                    if($this->_add === 'ALL' && $this->_complement === 'B1W4sA'){
                        $response = UserModel::deleteAllUsers();
                    }
                    else{
                        $response = UserModel::deleteUser($this->_complement);
                    }
                    
                    break;
                default:
                    $response = 104;
            }
            ResponseController::response($response);
        }
        catch(Exception $e){
            ResponseController::response((int)$e->getMessage());
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