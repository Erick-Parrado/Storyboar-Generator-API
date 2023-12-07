<?php
class EndpointController{
    protected $_method;
    protected $_complement;
    protected $_data;
    protected $_add;
    protected $_fields;
    private $_codBase;
    private $_strictFields;

    function __construct($codBase,$method, $complement=null, $data=null,$add=null,$fields=null){
        $this->_method = $method;
        
        $this->_complement = $complement == null ? 0: $complement;
        $this->_data = $data;
        $this->_add = $add;
        $this->_fields = $fields;
        $this->_codBase = $codBase;
    }

    protected function dataExist(){
        if($this->_data == null) throw new Exception($this->_codBase+99);
    }

    protected function validateFields(){
        $this->dataExist();
        $dataAO = new ArrayObject($this->_data);
        $iter = $dataAO -> getIterator();
        while($iter->valid()){
            if(!array_key_exists($iter->key(),$this->_fields)){
                throw new Exception($this->_codBase+20);
            }
            $iter->next();
        }
    }
    
    protected function validateValues(){
        $this->strictFields();
        if(is_array($this->_data)){
            $dataAO = new ArrayObject($this->_data);
            $iter = $dataAO -> getIterator();
            $index = 1;
            while($iter->valid()){
                $pattern = (array_key_exists($iter->key(),$this->_fields))?$this->_fields[$iter->key()]:null;
                if(isset($pattern)){
                    $result = preg_match($pattern,$iter->current());
                    if(!$result) {
                        throw new Exception($this->_codBase+20+$index);
                    };
                    $index++;
                }
                $iter->next();
            }
        }
    }

    //Obligatoria al usar strictFields()
    protected function setStrict($strictFields){
        $this->_strictFields = $strictFields;
    }

    protected function strictFields(){
        if($this->_strictFields != null){
        }
        else{
            $this->_strictFields = array_keys($this->_fields);
        }
        $this->validateFields();
        $fieldsAO = new ArrayObject($this->_strictFields);
        $iter = $fieldsAO -> getIterator();
        while($iter->valid()){
            if(!array_key_exists($iter->current(),$this->_data)){
                throw new Exception($this->_codBase+29);
            }
            $iter->next();
        }
    }
}
?>