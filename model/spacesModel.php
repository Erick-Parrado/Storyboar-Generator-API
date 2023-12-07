<?php
require_once 'model/projectModel.php';
require_once 'controller/endpointController.php';

class ProjectController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(       
            "scen_id",
            "scen_number",
            "scen_duration",
            "scen_place",
            "scen_argument",
            "dayT_id",
            "spac_id",
            "proj_id"
        );
        parent::__construct(400,$method,$complement,$data,$add,$fields);    
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
}
?>