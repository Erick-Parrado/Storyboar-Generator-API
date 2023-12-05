<?php
class EndpointController{
    protected $_method;
    protected $_complement;
    protected $_data;
    protected $_add;
    protected $_fields;
    private $_codBase;

    function __construct($codBase,$method, $complement=null, $data=null,$add=null,$fields){
        $this->_method = $method;
        
        $this->_complement = $complement == null ? 0: $complement;
        $this->_data = $data;
        $this->_add = $add;
        $this->_fields = $fields;
        $this->_codBase = $codBase;
    }

    protected function strictFields(){
        $fieldsAO = new ArrayObject($this->_fields);
        $iter = $fieldsAO -> getIterator();
        while($iter->valid()){
            if(!in_array($iter->current(),array_keys($this->_data))){
                throw new Exception($this->_codBase+29);
            }
            $iter->next();
        }

    }
    

    protected function validateFields(){
        $dataAO = new ArrayObject($this->_data);
        $iter = $dataAO -> getIterator();
        while($iter->valid()){
            if(!in_array($iter->key(),$this->_fields)){
                throw new Exception($this->_codBase+20);
            }
            $iter->next();
        }
    }
    
    protected function existData(){
        if($this->_data == null) throw new Exception($this->_codBase+99);
    }

}
?>