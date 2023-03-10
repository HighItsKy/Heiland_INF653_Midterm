<?php
/*
//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');*/
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');
/*
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();*/

//Instantiate author object
$author = new Author($db);

//Get raw posted data
$data = json_decode(file_get_contents("php://input"));

//Set ID to update
$author->id = $data->id;
$author->author = $data->author;

//Update post
if($author->update()){
    echo json_encode(array('message' => 'Author Updated'));
}
else{
    echo json_encode(array('message' => 'Author Not Updated'));
}