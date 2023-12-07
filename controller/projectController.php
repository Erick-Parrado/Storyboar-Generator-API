<?php
require_once 'model/projectModel.php';
require_once 'controller/endpointController.php';

class ProjectController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(       
            "user_email"=>"/^([a-zA-Z0-9_.]{8,})@([a-z]{5,})\.([a-z]{2,3})(\.[a-z]{2,3})?$/",
            "user_pass"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/",
        );
        var_dump($fields);
        parent::__construct(600,$method,$complement,$data,$add,$fields);    
    }

    public function index(){
        echo ':v';
    }
}
?>