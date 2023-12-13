<?php
require_once 'model/userModel.php';

class loginController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(       
            "user_email"=>"/^([a-zA-Z0-9!\"#$%&'()*,_.\-\/]{3,})@([a-z]{5,})\.([a-z]{2,3})(\.[a-z]{2,3})?$/",
            "user_pass"
        );
        parent::__construct(600,$method,$complement,$data,$add,$fields);    
    }

    public function index(){
        $response = 0;
        switch($this->_method){
            case 'POST':
                $strictFields= array(
                    "user_email",
                    "user_pass"
                );
                $this->needNone();
                $this->setStrict($strictFields);
                $this->strictFields();
                $response = UserModel::login($this->_data);
                break;
            default:
                throw new Exception(104);
        }
        ResponseController::response($response);
    }
}
?>