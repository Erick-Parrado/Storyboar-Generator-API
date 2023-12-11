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
            "user_pass",
            "user_phone"=>"/^[0-9]{10}$/",
            'us_identifier',
            'us_key'
        );
        parent::__construct(200,$method,$complement,$data,$add,$fields);
    }

    public function index(){
        $response = 0;
        switch ($this->_method){
            case 'GET':
                $this->optionalComplement();
                $response = UserModel::readUser($this->_complement);
                break;
            case 'POST':
                $this->needNone();
                $strictFields = array(
                    'user_name','user_lastName'
                    ,'user_email','user_pass',
                    'user_phone'
                );
                $this->setStrict($strictFields);
                $this->strictFields();
                $response = UserModel::createUser($this->_data);
                break;
            case 'PUT':
                $this->needComplement();
                $this->validateValues();
                $response = UserModel::updateUser($this->_complement,$this->_data);
                break;
            case 'DELETE':
                $this->needComplement();
                $response = UserModel::deleteUser($this->_complement);
                break;
            default:
                throw new Exception(104);
        }
        ResponseController::response($response);
    }
}

?>