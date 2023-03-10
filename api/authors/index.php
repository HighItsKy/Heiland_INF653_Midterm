<?php
//Keeps CORS from blocking automated tests.
header('Access-Control-Allow-Origin: *'); //Indicates that the response can be shared with the requesting code from the origin.
header('Content-Type: application/json'); //Informs the browser on what kind of data it should expect.
$method = $_SERVER['REQUEST_METHOD']; //Gets the value of the HTTP request.

//If the HTTP request is an OPTIONS request:
if($method === 'OPTIONS'){
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); //Indicates that GET, POST, PUT and DELETE are permitted methods to use.
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With'); //Allows access to the origin, accept, content-type, and x-requested with headers.
    exit();
}

//If the method is a GET request:
if($method === "GET"){
    //If the URL has a parameter, then it is only looking to read one entry.
    if(isset($_GET['id'])){
        include_once 'read_single.php';
    }
    else //If the URL has no parameters, it is wanting to obtain all entries.
        include_once 'read.php';
}
else if($method === "PUT"){ //If the method is a PUT request, the update.php file is included.
    include_once 'update.php';
}
else if($method === "DELETE"){ //If the method is a DELETE request, the delete.php file is included.
    include_once 'delete.php';
}
else if($method === "POST"){ //If the method is a POST request, the create.php file is included.
    include_once 'create.php';
}
else //If the method is not any of those requests, then a warning message is echoed.
    echo("Incorrect method being used. It must be either GET, PUT, DELETE, or POST.");