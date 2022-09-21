<?php

/***** HTTP Response Contruct *****/
/* Response */
function status($code, $message = ''){
    $status = 'HTTP/1.1' . ' ' . $code . ' ' . $message;
    return $status;
}

function headers($statusCode, $statusMessage=''){
    header(status($statusCode, $statusMessage));
    header("Content-Type: application/json");
}

function response($statusCode, $statusMessage='', $data=null){
    headers($statusCode, $statusMessage);
    if ($data != null){
        echo json_encode($data);
    }
    exit;
}
/***** END of HTTP Response Contruct *****/


/***** URI *****/
/* Clear URI last slash */
function clearUriSlash(){
    $uri = $_SERVER["REQUEST_URI"];
    if (substr($uri, -1)=='/'){
        return substr_replace($uri ,"", -1);
    } else {
        return $uri;
    }
}

/* Get last part of uri - IF Number */
function hasUriNumParam(){
    $uri = clearUriSlash();
    if(is_numeric(basename($uri))){
        return array("uri"=>implode("/",explode("/",$uri,-1)), 'numParam'=>basename($uri));
    }else{
        return array("uri"=>parse_url($uri, PHP_URL_PATH), 'numParam'=>'');
    }
}

/* Return clear URI for Router */
function requestUri(){
    return hasUriNumParam();
}

/* Return clasic params */
function  requestUriParams(){
    parse_str(parse_url($_SERVER["REQUEST_URI"])['query'],$params);
    return $params;    
}
/***** END of URI *****/





/* Request Method for Controllers */
function requestMethod($method){
        $supported = Array('GET','POST','PUT','DELETE');
        if (in_array($_SERVER["REQUEST_METHOD"],$supported)){
            if(strtolower($_SERVER["REQUEST_METHOD"])===strtolower($method)){
                return true;
            } else{
                return false;
            }
        } else{
            response(405);
        }
        
        return false;
}

/***** DB Operations *****/
/* DB connect */
function database(){ 
    try {
        $conn = new mysqli('localhost', 'root', '','api');
        if ($conn->connect_error) {
            response(500,'DB Connection error');
        }else{
            return $conn;
        }
    } catch (Exception $e) {
        response(500,'DB Connection unavailable');
    }
}

/* DB Query for Controllers */


function selectQuery($query, $params){
    $db = database();
    $query = $db->prepare($query);
    $query->execute($params);
    $results = $query->get_result()->fetch_all(MYSQLI_ASSOC);
    mysqli_close($db);
    return $results;
}

function iduQuery($query, $params){
    $db = database();
    $query = $db->prepare($query);
    if ($query->execute($params)){
        mysqli_close($db);
        return true;
    } else{
        return false;
    }
    
}

function isRecordUniq($column,$table,$value){
    try {
        $queryConstruct = 'SELECT ' . $column . ' FROM ' . $table . ' WHERE ' . $column . '=?';
        $uniq = selectQuery($queryConstruct, array($value));
        if (empty($uniq)){
            return true;
        }else{
            return false;
        }
    }
    catch (Exception $e){
        response(400,'Could not get data from database');
    }
}
/***** END of DB Operations *****/
?>