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
        $this->_strictFields = null;
    }
    
    //Petition validation
    protected function needNone(){
        if($this->_complement != 0 || $this->_add != null) throw new Exception(104);
    }
    protected function optionalComplement(){
        if(!is_numeric($this->_complement) || $this->_add != null) throw new Exception(104);
    }

    protected function needComplement(){
        if((!is_numeric($this->_complement) && $this->_complement<=0) || $this->_add != null) throw new Exception(104);
    }

    protected function needAdd(){
        if(!is_numeric($this->_complement) && $this->_add == null) throw new Exception(104);
    }

    //Data validation
    protected function dataExist(){
        if($this->_data == null) throw new Exception($this->_codBase+99);
    }

    protected function validateFields(){
        $this->dataExist();
        $dataAO = new ArrayObject($this->_data);
        $iter = $dataAO -> getIterator();
        while($iter->valid()){
            if(
            !array_key_exists($iter->key(),$this->_fields) 
            && 
            !in_array($iter->key(),$this->_fields)){
                throw new Exception($this->_codBase+20);
            }
            $iter->next();
        }
    }
    
    protected function validateValues(){
        $this->validateFields();
        if(is_array($this->_data)){
            $dataAO = new ArrayObject($this->_data);
            $iter = $dataAO -> getIterator();
            $index = 1;
            while($iter->valid()){
                if(is_string($iter->key())){
                    $pattern = (array_key_exists($iter->key(),$this->_fields))?$this->_fields[$iter->key()]:null;
                    if(isset($pattern)){
                        $result = preg_match($pattern,$iter->current());
                        if(!$result) {
                            throw new Exception($this->_codBase+20+$index);
                        };
                        $index++;
                    }
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
        $this->validateValues();
        if($this->_strictFields == null){
            $this->_strictFields = $this->_fields;
        }
        $fieldsAO = new ArrayObject($this->_strictFields);
        $iter = $fieldsAO -> getIterator();
        while($iter->valid()){
            if(is_numeric($iter->key())){
                if(!array_key_exists($iter->current(),$this->_data)){
                throw new Exception($this->_codBase+29);
                }
            }
            else{
                if(!array_key_exists($iter->key(),$this->_data)){
                    throw new Exception($this->_codBase+29);
                }
            }
            
            $iter->next();
        }
    }
}
?>