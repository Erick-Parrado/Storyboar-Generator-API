<?php
require_once 'model/sceneModel.php';
require_once 'model/projectModel.php';
require_once 'controller/endpointController.php';
require_once 'model/dayTimeModel.php';
require_once 'model/spaceModel.php';

class SceneController extends EndpointController{
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
        $response = 0;
        switch($this->_method){
            case 'GET':
                $this->needAdd();
                switch($this->_add){
                    case 'daytimes':
                        $response = DayTimeModel::readDayTimes($this->_complement);
                        break;
                    case 'spaces':
                        $response = SpaceModel::readSpaces($this->_complement);
                        break;
                    default:
                        if(!is_numeric($this->_add))throw new Exception(104);
                        $response = SceneModel::readScene($this->_complement,$this->_add);
                        break;
                }
                break;
            case 'POST':
                $this->needComplement();
                $strictFields = array(
                    'scen_number',
                    'scen_duration',
                    'scen_place',
                    'dayT_id',
                    'spac_id',
                    'scen_argument'
                );
                $this->setStrict($strictFields);
                $this->strictFields();
                $response = SceneModel::createScene($this->_complement,$this->_data);
                break;
            case 'PUT':
                $this->needAdd();
                $this->validateFields();
                $response = SceneModel::updateScene($this->_complement,$this->_add,$this->_data);
                break;
            case 'DELETE':
                $this->needAdd();
                $response = SceneModel::deleteScene($this->_complement,$this->_add);
                break;
            default:
            throw new Exception(104);
        }
        ResponseController::response($response);
    }

}
?>