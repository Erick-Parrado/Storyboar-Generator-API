<?php

class UserController{
    private $_method; //get,post, put
    private $_complement; //get user 1 o 2
    private $_data; //Datos a insertar o actualizar

    function __construct($method, $complement, $data){
        $this->_method = $method;
        $this->_complement = $complement == null ? 0: $complement;
        $this->_data = $data !=0 ? $data:"";
    }

    public function index(){
        if($this->validateData()){
            switch ($this->_method){
                case "GET":
                    switch ($this->_complement){
                        case 0: 
                            ResponseController::response(201,UserModel::getUsers(0));
                            return;
                        default:
                            ResponseController::response(201,UserModel::getUsers($this->_complement));
                            return;
                    }
                case "POST":
                    UserModel::createUser($this->generateSalting());
                    ResponseController::response(202);
                    return;
                case "PUT":
                    ResponseController::response(UserModel::updateUser($this->_complement,$this->generateSalting()));
                    return;
                case "DELETE":
                    ResponseController::response(UserModel::deleteUser($this->_complement));
                    return;
                case "PATCH":
                    ResponseController::response(UserModel::activeUser($this->_complement));
                    return;
                default:
                    ResponseController::response(404);
            }
        }
    }

    private function validateData(){
        $patterns = array("user_mail"=>"/^[a-zA-Z0-9_.]{8,}@gmail.com$/","user_pss"=>"/^(?=.*[a-z]+)(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[!@#$%^&*(){}\\[\\]]+)[a-zA-Z0-9!@#$%^&*(){}\\[\\]]{8,}$/","user_phone"->"/^[0-9]{7}$/");
        if(is_array($this->_data)){
            $dataAO = new ArrayObject($this->_data);
            $iter = $dataAO -> getIterator();
            while($iter->valid()){
                $pattern = (isset($patterns[$iter->key()]))?$patterns[$iter->key()]:null;
                if(isset($pattern)){
                    $result = preg_match($pattern,$iter->current());
                    if(!$result) {
                        ResponseController::response(101,$iter->key());
                        return false;
                    };
                }
                $iter->next();
            }
        }
        return true;
    }


    private function generateSalting(){
        $trimmed_data="";
        if(($this->_data !="") || (!empty($this->_data))){
            $trimmed_data = array_map('trim', $this->_data);
            if(isset($this->_data['user_pss'])){
                $trimmed_data['user_pss'] = md5($trimmed_data['user_pss']);
                $key = str_replace("$","ERT",crypt($trimmed_data['user_pss'],'uniempresa$'));
                $trimmed_data['us_key']=$key;
            }
            if(isset($this->_data['user_mail'])){
                $identifier = str_replace("$","y78",crypt($trimmed_data['user_mail'],'$1$aserwtop$'));
                $trimmed_data['us_identifier']=$identifier;
            }
            return $trimmed_data;
        }
        return $this->_data;
    }
}

?>