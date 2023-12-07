<?php
require_once 'model/projectModel.php';
require_once 'controller/endpointController.php';

class ProjectController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(       
            "proj_id",
            "proj_tittle",
            "proj_producer",
            "proj_description",
            "proj_dateUpdate",
            "proj_pin"
        );
        parent::__construct(300,$method,$complement,$data,$add,$fields);    
    }

    public function index(){
        try{
            $response = 0;
            if(!is_numeric($this->_complement)){
                throw new Exception(104);
            }
            switch($this->_method){
                case 'GET':
                    $response = ProjectModel::readProject($this->_complement);
                    break;
                case 'POST':
                    $strictFields= array(
                        "proj_tittle",
                        "proj_producer",
                        "proj_description"
                    );
                    $this->setStrict($strictFields);
                    $this->strictFields();
                    $this->generatePIN();
                    $this->makeUpdate();
                    $response = ProjectModel::createProject($this->_data);
                    break;
                case 'PUT':
                    $this->validateFields();
                    $response = ProjectModel::updateProject($this->_complement,$this->_data);
                    break;
                case 'DELETE':
                    $response = ProjectModel::deleteProject($this->_complement);
                    break;
                default:
                    $response = 104;
            }
            ResponseController::response($response);
        }catch(Exception $e){
            ResponseController::response($e->getMessage());
        }
    }

    private function generatePIN(){
        do{
            $pin = strtoupper(bin2hex(random_bytes(4)));
        }while(ProjectModel::pinExist($pin));
        
        $this->_data['proj_pin'] = $pin;
    }

    private function makeUpdate(){
        $this->_data['proj_dateUpdate'] = date('d/m/Y', time());
    }
}
?>