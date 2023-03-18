<?php
/*
//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');*/
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();

//Instantiate quote object
$quote = new Quote($db);

//Get raw quote data
$data = json_decode(file_get_contents("php://input"));

if(!get_object_vars($data) || !isset($data->id)){ //If there are no parameters
    echo json_encode(array('message' => 'Missing Required Parameters'));
}
else{
    $quote->id = $data->id;

    //Create post
    if(!$quote->delete())
    {
        echo json_encode(array('message' => 'Quote Not Deleted'));
    }
}