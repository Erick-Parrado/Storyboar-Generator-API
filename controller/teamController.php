<?php
require_once 'controller/endpointController.php';
require_once 'model/projectModel.php';
require_once 'model/userModel.php';
require_once 'model/teamModel.php';

class TeamController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(
            'team_id',
            'role_id',
            'user_id',
            'proj_id'
        );
        parent::__construct(700,$method,$complement,$data,$add,$fields);
    }

    public function index(){
        try{
            $response = 0;
            switch($this->_method){
                case 'POST':
                    if($this->_add != null && $this->_complement != null){
                       $response = TeamModel::accessProject($this->_complement,$this->_add);
                    }
                    break;
            }
            ResponseController::response($response);
        }catch(Exception $e){
            ResponseController::response((int)$e->getMessage());
        }
        
    } 
}
?>