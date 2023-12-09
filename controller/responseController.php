<?php
/*
100 Servidor
200 Usuarios
300 Proyecto
400 Escena
500 Plano
600 Login
700 Team
900 Base de datos
*/

class ResponseController{   

    static private $_response=null;
    static private $_cod = null;
    static private $_extra = null;

    static public function response($cod,$extra=null){
        if(is_array($cod)){
            self::$_cod = $cod[0];
            self::$_extra = $cod[1];
        }
        else{
            self::$_cod = $cod;
            self::$_extra = $extra;
        }
        
        switch(self::$_cod){
            case 0:
                self::setError('Response has\'t be caught');
                break;
            case 104:
                self::setError('Route not found');
                break;
            case 114:
                self::setError('NO TIENE CREDENCIALES');
                break;
            case 115:
                self::setError('NO TIENE ACCESO');
                break;
            case 200://Post Users
                self::setInfo('Usuario creado');
                break;
            case 201://Get Users
                self::setResult(self::$_extra);
                break;
            case 202://Put Users
                self::setInfo('Usuario actualizado');
                break;
            case 203://Delete Users
                self::setInfo('Usuario eliminado');
                break;
            case 209:
                self::setInfo('Usuario ya existe');
                break;
            case 210://Delete All Users
                self::setInfo('Base de datos reiniciada');
                break;
            case 219:
                self::setError('El usuario no existe');
                break;
            case 220:
                self::setError('Campos no reconocidos en la data de users');
                break;
            case 221:
                self::setError('user_email no cumple con las condiciones mínimas',
                    array(
                        'Usuario de al menos 8 cárteres',
                        'Dominio simple o doble'
                ));
                break;
            case 222:
                self::setError('user_pass no cumple con las condiciones mínimas',
                    array(
                        'Al menos una minúscula',
                        'Al menos una mayúscula',
                        'Al menos un numero',
                        'Al menos un carácter especial',
                        'Longitud de al menos 8 cárteres'
                ));
                break;
            case 223:
                self::setError('user_phone no cumple con las condiciones mínimas',
                    array(
                    'Longitud de 10 dígitos',
                    'Unicamente números'
                ));
                break;
            case 229:
                self::setError('Para la creacion de usuarios requiere todos los campos',
                    array(
                        'user_name',
                        'user_lastName',
                        'user_email',
                        'user_pass',
                        'user_phone'
                ));
                break;
            case 300: //Post Projects
                self::setInfo('Proyecto creado');
                break;
            case 301://Get Projects
                self::setResult(self::$_extra);
                break;
            case 302:
                self::setInfo('Proyecto actualizado');
                break;
            case 303:
                self::setInfo('Proyecto eliminado');
                break;
            case 304:
                self::setInfo('PIN actualizado');
                break;
            case 309:
                self::setError('Este proyecto ya existe en la productora');
                break;
            case 319:
                self::setError('El proyecto no existe');
                break;
            case 320:
                self::setError('Campos no reconocidos en la data de projects');
                break;
            case 329:
                self::setError('Para crear un project requiere todos los campos',
                    array( 
                        "proj_tittle",
                        "proj_producer",
                        "proj_description"
                ));
                break;
            case 400:
                self::setInfo('Escena creada');
                break;
            case 401:
            case 402:
                self::setResult(self::$_extra);
                break;
            case 403:
                self::setInfo('Escena actualizada');
                break;
            case 404:
                self::setInfo('Escena eliminada');
                break;
            case 419:
                self::setError('Escena no existe');
                break;
            case 420:
                self::setError('Campos no reconocidos en la data de scenes');
                break;
            case 421:
                self::setError('scen_number no es valido');
                break;
            case 425:
                self::setError('spac_id no es valido');
                break;
            case 426:
                self::setError('dayT_id no es valido');
                break;
            case 428:
                self::setError('Se requiere proj_id');
                break;
            case 429:
                self::setError('Para crear usa scene requiere todos los campos',
                    array( 
                        'scen_number',
                        'scen_duration',
                        'scen_place',
                        'dayT_id',
                        'spac_id',
                        'scen_argument',
                        'proj_id'
                ));
                break;
            case 500:
                self::setError('Plano creado');
                break;
            case 501:
            case 502:
                self::setResult(self::$_extra);
                break;
            case 503:
                self::setError('Plano actualizado');
                break;
            case 519:
                self::setError('Plano no existe');
                break;
            case 520:
                self::setError('Campos no reconocidos en la data de planes');
                break;
            case 521:
                self::setError('plan_number no es valido');
                break;
            case 528:
                self::setError('Se requiere scen_number');
                break;
            case 529:
                self::setError('Para crear un plane requiere todos los campos',
                    array( 
                        "plan_number",
                        "plan_duration",
                        "plan_description",
                        "plan_image",
                        "shot_id",
                        "scen_id",
                        "move_id"
                ));
                break;
            case 600:
                self::setInfo('Inicio de sesion','OK');
                self::$_response['credentials'] = self::$_extra;
                break;
            case 604:
                self::setError('ERROR EN CREDENCIALES');
                self::$_response['credentials'] = null;
                self::$_response['header'] = "HTTP/1.1 400 FAIL";
                break;
            case 621:
            case 622:
                self::codeChange(604);
                break;
            case 620:
                self::setError('Campos no reconocidos para el inicio de sesion');
                break;
            case 629:
                self::setError('Para inicio de sesion se requiere:',array(
                    'user_email',
                    'user_pass'
                ));
                break;
            case 700://Ingreso 
                self::setInfo('Se ingreso exitosamente'); 
                break; 
            case 701://Get Users in Teams
            case 702:
                self::setResult(self::$_extra);
                self::setResult(self::$_extra);
                break;
            case 703://Put Team in Teams
                self::setInfo('Se actualizo rol');
                break;
            case 704://Delete User from Team
                self::setInfo('Se eliminio miembro');
                break;
            case 709://Ingreso 
                self::setError('Ya se ha creado el acceso'); 
                break;  
            case 710://Ingreso 
                self::setError('El acceso no existe'); 
                break;  
            case 720:
                self::setError('Campos no reconocidos para acceso de proyectos');
                break;   
            case 721:
                self::setError('Se requiere id');
                break;   
            case 722:
                self::setError('Se requiere especificar consulta');
                break;    
            case 729:
                self::setError('Para el acceso se requiere:',array(
                    'user_id',
                    'proj_pin'
                ));  
                break;    
            case 751:
                self::setResult(self::$_extra);
                break;
            case 759:
                self::setError('Rol no existe');
                break;
            case 299:
            case 399:
            case 499:
            case 599:
            case 699:
            case 799:
                self::setError('Data no ha sido proveída');
                break;
            case 900:
                self::setInfo('Success connection');
                break;
            case 904:
                self::setInfo('Unknown service');
                break;
            case 909:
                self::setInfo('Connection error');
                self::$_response['info'] = self::$_extra;
                break;
            case 910:
                self::setInfo('SQL Error',self::$_extra);
                break;
            default:
                self::setError('Mensaje no identificado :v',self::$_extra);
        }
        echo json_encode(self::$_response,JSON_UNESCAPED_UNICODE);
    }

    static private function setInfo($message,$details = null){
        self::$_response['info']['status']=self::$_cod;
        self::$_response['info']['message']=$message;
        if($details!=null)self::$_response['info']['details']=$details;
    }

    static private function setError($message,$info=null){
        self::$_response['error']['status']=self::$_cod;
        self::$_response['error']['message']=$message;
        if($info!=null){
            self::$_response['error']['info']=$info;
        }
    }
    
    static private function setResult($statement){
        self::$_response['info']['status']=self::$_cod;
        self::$_response['info']['count']=$statement->rowCount();
        self::$_response['response']=$statement->fetchAll();

    }

    static private function codeChange($cod){
        self::$_cod = $cod;
        self::response(self::$_cod);
    }
}
?>