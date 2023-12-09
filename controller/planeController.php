<?php
require_once 'controller/endpointController.php';
require_once 'model/planeModel.php';
require_once 'model/shotModel.php';
require_once 'model/framingModel.php';
require_once 'model/moveModel.php';

class PlaneController extends EndpointController{
    private $_more;

    function __construct($method, $complement=null, $data=null,$add=null,$more=null){
        $fields = array(       
            "plan_id",
            "plan_number",
            "plan_duration",
            "plan_description",
            "plan_image",
            "shot_id",
            "move_id",
            "fram_id",
            "scen_id"
        );
        $this->_more = $more;
        parent::__construct(500,$method,$complement,$data,$add,$fields);    
    }

    public function index(){
        try{
            $response = 0;
            switch($this->_method){
                case 'GET':
                    switch($this->_add){
                        case 'scene':
                            $this->needAdd();
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
                            if($this->_more != null){
                                $this->needMore();
                                $response = PlaneModel::readPlane($this->_complement,$this->_add,$this->_more);
                            }
                            else{
                                $this->needAdd();
                                $response = PlaneModel::readScenePlanes($this->_complement,$this->_add);
                            }
                            break;
                    }
                    break;
                case 'POST':
                    $this->needAdd();
                    $strictFields = array(   
                        "plan_number",
                        "plan_duration",
                        "plan_description",
                        "plan_image",
                        "shot_id",
                        "move_id",
                        "fram_id"
                    );
                    $this->setStrict($strictFields);
                    $this->strictFields();
                    $response = PlaneModel::createPlane($this->_add,$this->_complement,$this->_data);
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
            ResponseController::response($e->getMessage());
        }
    }
    protected function needAdd(){
        parent::needAdd();
        if($this->_more!=null) throw new Exception(104);
    }

    private function needMore(){
        parent::needAdd();
        if($this->_more==null || !is_numeric($this->_add)) throw new Exception(104);
    }
}
?>