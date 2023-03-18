<?php
//Header
header('Access-Control-Allow-Methods: POST');
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

if(!get_object_vars($data) || !isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)){ //If there are no parameters
    echo json_encode(array('message' => 'Missing Required Parameters'));
}
else{
    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    //Create quote
    if(!$quote->create()){
        echo json_encode(array('message' => 'Quote Not Created'));
    }
}