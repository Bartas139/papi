<?php

switch ($_SERVER["REQUEST_METHOD"]) {
    case '':
    case 'GET':
        if (requestMethod('get')){
            $db = database();
            /* Get Single User */
            if (requestUri()['numParam'] != ''){
                try {
                    $user = selectQuery('SELECT * FROM users WHERE id = ?',array(requestUri()['numParam']));
                    if (empty($user)){
                        response(404, 'User with given ID not found');
                    }else{
                        response(200, null, $user);
                    }
                }
                catch (Exception $e){
                    response(400,'Could not get data from database');
                }
            /* Get all users */
            }else{
                try {
                    $users = selectQuery('SELECT * FROM users', array());
                    if (empty($users)){
                        response(404, 'No users found');
                    }else{
                        response(200, null, $users);
                    }
                }
                catch (Exception $e){
                    response(400,'Could not get data from database');
                }
            }
            
        }

    case 'POST':
        if (requestMethod('post')){
            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data['email']) || $data['email'] == ''){
                response(400,'Email is required');
            } elseif (!isset($data['password']) || $data['password'] == ''){
                response(400,'Password is required');
            } else {
                if (!isRecordUniq('email','users',$data['email'])){
                    response(409, 'User already exists');
                }else{
                    try {
                        $result = iduQuery('INSERT INTO users (email, password) VALUES (?,?);', array($data['email'],$data['password']));
                        if (!$result){
                            response(400, 'User was not created');
                        }else{
                            response(201, 'User was created');
                        }
                    }
                    catch (Exception $e){
                        response(400,'Could not get data from database');
                    }
                }
            }
        
        }

    case 'PUT':
        /* Přepsat a lépe!! */
        if (requestMethod('put')){
            $data = json_decode(file_get_contents("php://input"), true);
            $queryParams = Array();
            $queryParamsPlaceholder = Array();
            if (!isset($data['email']) && !isset($data['password'])){
                response(400,'Atlest one change require');
            } else{
                if(isset($data['email'])){
                    array_push($queryParamsPlaceholder, 'email=?');
                    array_push($queryParams, $data['email']);
                }
                if(isset($data['password'])){
                    array_push($queryParamsPlaceholder, 'password=?');
                    array_push($queryParams, $data['password']);
                }
                if (requestUri()['numParam'] != ''){
                    array_push($queryParams, requestUri()['numParam']);
                    try {
                        $result = iduQuery('UPDATE users SET '. implode(',', $queryParamsPlaceholder) .' WHERE id = ?', $queryParams);
                        if (!$result){
                            response(400, 'User has not been updated');
                        }else{
                            response(200, 'User updated');
                        }
                    }
                    catch (Exception $e){
                        response(400,'Could not get data from database');
                    }
                }
                else{
                    response(400, 'User ID required');
                }
            }
        
        }

    case 'DELETE':
        if (requestMethod('delete')){
            if (requestUri()['numParam'] != ''){
                try {
                    $result = iduQuery('DELETE FROM users WHERE id = ?',array(requestUri()['numParam']));
                    if (!$result){
                        response(400, 'User has not been deleted');
                    }else{
                        response(200, 'User deleted');
                    }
                }
                catch (Exception $e){
                    response(400,'Could not get data from database');
                }
            }
            else{
                response(400, 'User ID required');
            }
        }
        
    default:
        response(405);
        break;
}
?>