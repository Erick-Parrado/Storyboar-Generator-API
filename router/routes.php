<?php
//require_once "controller/loginController.php";

$routesArray = array_filter(explode("/",$_SERVER['REQUEST_URI']));

//Data
$data = array();
$data['raw_input'] = @file_get_contents('php://input');
$_POST = json_decode($data['raw_input'], true);


//Validacion de endpoint
if ($routesArray[3] == ''){
    ResponseController::response(104);
}else{
    $endPoint = (array_filter($routesArray)[3]);
    $complement =  (array_key_exists(4,$routesArray))?($routesArray)[4]:0;
    $add  = (array_key_exists(5,$routesArray))?($routesArray)[5]:"";
    echo 'endPoint:'.$endPoint;
    echo 'add:'.$add;
    echo 'complement:'.$complement;

    if($add !="") $complement .= "/".$add;
        $method = $_SERVER['REQUEST_METHOD'];
        switch($endPoint){
            case 'users':
                //$user = new  UserController($method, $complement, $_POST);
                //$user =  new UserController($method, $complement, 0);
                //$user -> index();
                break;

            case 'projects':
                if(isset($_POST))
                    //$user = new  UserController($method, $complement, $_POST);
                //else
                    //$user =  new UserController($method, $complement, 0);
                //$user -> index();
                break;
                
            case 'scenes':
                if(isset($_POST))
                    //$user = new  UserController($method, $complement, $_POST);
                //else
                    //$user =  new UserController($method, $complement, 0);
                //$user -> index();
                break;
                
            case 'planes':
                if(isset($_POST))
                    //$user = new  UserController($method, $complement, $_POST);
                //else
                    //$user =  new UserController($method, $complement, 0);
                //$user -> index();
                break;
                
            case 'access':
                if(isset($_POST))
                    //$user = new  UserController($method, $complement, $_POST);
                //else
                    //$user =  new UserController($method, $complement, 0);
                //$user -> index();
                break;
        
            case 'login':
                if(isset($_POST) && $method=='POST'){
                    $user = new LoginController($method, $_POST);
                    $user -> index();
                }else{
                    ResponseController::response(104);
                    return;
                }
                break;
            default:
            ResponseController::response(104);
            return;
    }
}
?>