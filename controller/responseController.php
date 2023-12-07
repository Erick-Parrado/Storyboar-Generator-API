<?php
/*
100 Servidor
200 Usuarios
300 Proyecto
400 Escena
500 Plano
600 Login
700 Acceso
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
            case 104:
                self::setError('Ruta no encontrada');
                break;
            case 109:
                self::setError('Exceso de parametros');
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
                        'Longitud de al menos 8 cárteres',
                        'Dominio gmail.com'
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
                self::setError('El enpoint de users requiere todos los campos',
                    array(
                        'user_id',
                        'user_name',
                        'user_lastName',
                        'user_email',
                        'user_pass',
                        'user_phone'
                ));
                break;
            case 299:
            case 699:
                self::setError('Data no ha sido proveída');
                break;
            case 600:
                self::setInfo('Inicio de sesion','OK');
                self::$_response['credentials'] = self::$_extra;
                break;
            case 604:
            case 621:
            case 622:
                self::$_cod = 604;
                self::setError('ERROR EN CREDENCIALES');
                self::$_response['credentials'] = null;
                self::$_response['header'] = "HTTP/1.1 400 FAIL";
                break;
            case 629:
                self::setError('Para inicio de sesion se requiere:',array(
                    'user_email',
                    'user_pass'
                ));
                break;
            case 620:
                self::setError('Campos no reconocidos para el inicio de sesion');
                break;
            case 900:
                self::setInfo('Conexión realizada');
                break;
            case 904:
                self::setInfo('Servicio desconocido');
                break;
            case 909:
                self::setInfo('Error de conexion');
                self::$_response['info'] = self::$_extra;
                break;
            case 910:
                self::setInfo('Error en sentencia SQL',self::$_extra);
                break;
            default:
                self::setError('Mensaje no identificado',self::$_extra);
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
}
?>