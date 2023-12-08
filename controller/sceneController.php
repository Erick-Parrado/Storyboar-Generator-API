<?php
require_once 'model/sceneModel.php';
require_once 'model/projectModel.php';
require_once 'controller/endpointController.php';

class SceneController extends EndpointController{
    function __construct($method, $complement=null, $data=null,$add=null){
        $fields = array(       
            "scen_id",
            "scen_number",
            "scen_duration",
            "scen_place",
            "scen_argument",
            "dayT_id",
            "spac_id"
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
                    break;
                case 'POST':
                    break;
                case 'PUT':
                    break;
                case 'DELETE':
                    break;
                case 'PATCH':
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