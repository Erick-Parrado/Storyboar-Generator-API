<?php
require_once 'model/Connection.php';
require_once 'controller/responseController.php';
require_once 'controller/routesController.php';
require_once 'model/userModel.php';

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers: Authorization');

$routesArray = array_filter(explode('/',$_SERVER['REQUEST_URI']));
$count = count($routesArray);

switch ($count){
    case 2:
        if($routesArray[2]=='API'){
            if((Connection::doConnection())!=null)
                ResponseController::response(900);
        }
        else
            ResponseController::response(904);
        break;
    case 3:
    case 4:
    case 5:
    case 6:
        $endPoint = ($routesArray[3]);
        enterRoutes($endPoint);
        break;
    default:
        ResponseController::response(104);
}

function enterRoutes($endPoint){
    try{
        if($endPoint != 'login'){
            if(isset($_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_PW'])){
                $ok=false;
                $data['us_identifier'] = $_SERVER['PHP_AUTH_USER'];
                $data['us_key'] = $_SERVER['PHP_AUTH_PW'];
                $ok = UserModel::validateAuth($data);
                if($ok){
                    $routes = new RoutesController();
                    $routes -> index();
                }
                else{
                    throw new Exception(115);
                    return;
                }
            }
            else{
                throw new Exception(114);
                return;
            }
        }
        else{
            $routes = new RoutesController();
            $routes->index();
        }
    }
    catch(Exception $e){
        ResponseController::response($e->getMessage());
    }
}
?>