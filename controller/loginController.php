<?php
require_once 'model/userModel.php';
require_once 'controller/endpointController.php';

class loginController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(
            'user_id'=>"/^\d*$/",          
            "user_email"=>"/^([a-zA-Z0-9_.]{8,})@([a-z]{5,})\.([a-z]{2,3})(\.[a-z]{2,3})?$/",
            "user_pass"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/"
        );
        parent::__construct(600,$method,$complement,$data,$add,$fields);    
    }

    public function index(){
        try{
            $response = 0;
            switch($this->_method){
                case 'POST':
                    $response = UserModel::login($this->_data);
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
}
?>