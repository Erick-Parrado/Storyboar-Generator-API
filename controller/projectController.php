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
                    $this->optionalComplement();
                    $response = ProjectModel::readProject($this->_complement);
                    break;
                case 'POST':
                    $this->needNone();
                    $strictFields= array(
                        "proj_tittle",
                        "proj_producer",
                        "proj_description"
                    );
                    $this->setStrict($strictFields);
                    $this->strictFields();
                    $response = ProjectModel::createProject($this->_data);
                    break;
                case 'PUT':
                    $this->needComplement();
                    $this->validateFields();
                    $response = ProjectModel::updateProject($this->_complement,$this->_data);
                    break;
                case 'DELETE':
                    $this->needComplement();
                    $response = ProjectModel::deleteProject($this->_complement);
                    break;
                case 'PATCH':
                    $this->needAdd();
                    if($this->_add=='PIN'){
                        $response = ProjectModel::generatePIN($this->_complement);
                    }
                    break;
                default:
                    throw new Exception(104);
            }
            ResponseController::response($response);
        }catch(Exception $e){
            ResponseController::response($e->getMessage());
        }
    }

}
?>