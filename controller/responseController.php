<?php
/*
100 Servidor
200 Usuarios
300 Proyecto
400 Escena
500 Plano
900 Base de datos
*/

class ResponseController{   

    static private $_response=null;
    static private $_cod=null;

    static public function response($cod,$extra=null){
        self::$_cod = $cod;
        switch(self::$_cod){
            case 104:
                self::setError('Ruta no encontrada');
                break;
            case 110:
                self::setInfo('OK');
                self::$_response['credentials'] = $extra;
                break;
            case 114:
                self::setError('NO TIENE CREDENCIALES');
                break;
            case 115:
                self::setError('NO TIENE ACCESO');
                break;
            case 119:
                self::setError('ERROR EN CREDENCIALES');
                self::$_response['credentials'] = null;
                self::$_response['header'] = "HTTP/1.1 400 FAIL";
                break;
            case 200:
                self::setInfo('Usuario creado');
            case 209:
                self::setInfo('Usuario ya existe');
            case 220:
                self::setError('Campos no reconocidos en la data');
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
            case 299:
                self::setError('Data no ha sido proveída');
                break;
            case 900:
                self::setInfo('Conexión realizada');
                break;
            case 904:
                self::setInfo('Servicio desconocido');
                break;
            case 910:
                self::setInfo('Error en sentencia SQL');
                break;
            case 909:
                self::setInfo($extra);
                break;
            default:
                self::setError('Mensaje no identificado');
            /*case 101://Validacion de correo
                self::setError('El campo '.$statement.' no cumple con condiciones mínimas');
                break;
            case 201://Get Users
                self::setResult($statement);
                break;
            case 202://Create User
                self::setInfo('Usuario creado');
                break;
            case 203://Update User
                self::setInfo('Usuario actualizado');
                break;
            case 204://Delete User
                self::setInfo('Usuario elimindo');
                break;
            case 205://Delete User
                self::setInfo('Usuario activado');
                break;
            case 209: //Usuario no existe
                self::setError('Usuario no existe');
                break;
            case 404:
                self::setError('Ruta no encontrada');
                break;
            case 501:
            case 503://Error de credenciales
                self::setError('ERROR EN CREDENCIALES');
                self::$_response['credentials'] = null;
                self::$_response['header'] = "HTTP/1.1 400 FAIL";
                break;
            case 504://No credentials
                self::setError('NO TIENE CREDENCIALES');
                break;
            case 505:
                self::setError('NO TIENE ACCESO');
                break;*/
        }
        echo json_encode(self::$_response,JSON_UNESCAPED_UNICODE);
    }

    static private function setInfo($message){
        self::$_response['info']['status']=self::$_cod;
        self::$_response['info']['message']=$message;
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