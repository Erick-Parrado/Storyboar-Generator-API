<?php
require_once 'model/sceneModel.php';
require_once 'controller/endpointController.php';
require_once 'model/spacesModel.php';

class SceneController extends EndpointController{
    private $_more;

    function __construct($method, $complement=null, $data=null,$add=null,$more=null){
        $fields = array(       
            "plan_id",
            "plan_number",
            "plan_duration",
            "plan_description",
            "plan_image",
            "shot_id",
            "scen_id",
            "move_id",
            "scen_id"
        );
        $_more = $more;
        parent::__construct(400,$method,$complement,$data,$add,$fields);    
    }

    public function index(){
        try{
            $response = 0;
            switch($this->_method){
                case 'GET':
                    $this->needAdd();
                    switch($this->_add){
                        case 'scene':
                            //$response = SceneModel::readProjectScenes($this->_complement);
                            break;
                        case 'moves':
                            //$response = DayTimeModel::readDayTimes($this->_complement);
                            break;
                        case 'framings':
                            //$response = SpacesModel::readSpaces($this->_complement);
                            break;
                        case 'shots':
                            //$response = SpacesModel::readSpaces($this->_complement);
                            break;
                        default:
                            //if(!is_numeric($this->_add))throw new Exception(104);
                            //$response = SceneModel::readScene($this->_complement,$this->_add);
                            break;
                    }
                    break;
                case 'POST':
                    $this->needNone();
                    $strictFields = array(   
                        "plan_number",
                        "plan_duration",
                        "plan_description",
                        "plan_image",
                        "shot_id",
                        "scen_id",
                        "move_id"
                    );
                    $this->setStrict($strictFields);
                    $this->strictFields();
                    //$response = SceneModel::createScene($this->_data);
                    break;
                case 'PUT':
                    $this->needMore();
                    $this->validateFields();
                    //$response = SceneModel::updateScene($this->_complement,$this->_add,$this->_data);
                    break;
                case 'DELETE':
                    $this->needMore();
                    //$response = SceneModel::deleteScene($this->_complement,$this->_add,$this->_data);
                    break;
                default:
                    $response = 104;
            }
            ResponseController::response($response);
        }catch(Exception $e){
            ResponseController::response((int)$e->getMessage());
        }
    }

    private function needMore(){
        $this->needAdd();
        if($this->_more==null) throw new Exception(104);
    }
}
?>