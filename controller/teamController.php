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
                case 'GET':
                        if($this->_complement == null){
                            $response = 721;
                            break;
                        }
                        switch($this->_add){
                            case 'projects':
                                $response = TeamModel::readProjects($this->_complement);
                                break;
                            case 'users':
                                $response = TeamModel::readUsers($this->_complement);
                                break;
                            default:
                                $response = 722;
                        }
                    break;
                case 'POST':
                    if($this->_add != null && $this->_complement != null){
                       $response = TeamModel::accessProject($this->_complement,$this->_add);
                    }
                    $response = 104;
                    break;
                default:
                    $response = 104;
            }
            ResponseController::response($response);
        }catch(Exception $e){
            ResponseController::response((int)$e->getMessage());
        }
        
    } 
}
?>