<?php
require_once 'controller/userController.php';
require_once 'controller/loginController.php';
require_once 'controller/projectController.php';
require_once 'controller/teamController.php';
require_once 'controller/sceneController.php';

$routesArray = array_filter(explode("/",$_SERVER['REQUEST_URI']));

//Data
$data = array();
$data['raw_input'] = @file_get_contents('php://input');
$_POST = json_decode($data['raw_input'], true);

//Validacion de endpoint
if ($routesArray[3] == ''){
    ResponseController::response(104);
}else{
    $method = $_SERVER['REQUEST_METHOD'];
    $endPoint = (($routesArray)[3]);
    $complement =  (array_key_exists(4,$routesArray))?($routesArray)[4]:0;
    $add  = (array_key_exists(5,$routesArray))?($routesArray)[5]:"";

    // echo 'endPoint:'.$endPoint;
    // echo 'add:'.$add;
    //echo 'complement:'.$complement;

    $petition = null;

    switch($endPoint){
        case 'users':
            $petition = new UserController($method,$complement,$_POST,$add);
            break;
        case 'projects':
            $petition = new ProjectController($method,$complement,$_POST,$add);
            break;
        case 'scenes':
            $petition = new SceneController($method,$complement,$_POST,$add);
            break;
            
        case 'planes':
            echo 'Planes';
            break;
        case 'teams':
            $petition = new teamController($method,$complement,$_POST,$add);
            break;
        case 'login':
            $petition = new loginController($method,$complement,$_POST);
            break;
        default:
            ResponseController::response(104);
            return;
    }
    $petition->index();
}
?>