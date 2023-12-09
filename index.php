<?php
require_once 'model/Connection.php';
require_once 'controller/responseController.php';
require_once 'controller/routesController.php';

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
        $endPoint = ($routesArray[3]);
        enterRoutes($endPoint);
        break;
    default:
        ResponseController::response(109);
}

function enterRoutes($endPoint){
    if($endPoint != 'login'){
        if(isset($_SERVER['PHP_AUTH_USER']) && ($_SERVER['PHP_AUTH_PW'])){
            /*$ok=false;
            $identifier = $_SERVER['PHP_AUTH_USER'];
            $key =  $_SERVER['PHP_AUTH_PW'];
            $users = UserModel::getUserAuth();
            foreach ($users as $u){
                if($identifier.":".$key == $u["us_identifier"].":". $u["us_key"]){
                    $ok = true;
                }
            }
            if($ok){
                $routes = new RoutesController();
                $routes -> index();
            }
            else{
                ResponseController::response(505);
                return;
            }*/
            $routes = new RoutesController();
            $routes -> index();
        }
        else{
            ResponseController::response(115);
            return;
        }
    }
    else{
        $routes = new RoutesController();
        $routes->index();
    }
}
?>