<?php
//Header
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();

//Instantiate category object
$category = new Category($db);

//Get raw posted data
$data = json_decode(file_get_contents("php://input"));

if(!get_object_vars($data) || !isset($data->id) || !isset($data->category)){ //If there are no parameters
    echo json_encode(array('message' => 'Missing Required Parameters'));
}
else{
    //Set ID to update
    $category->id = $data->id;
    $category->category = $data->category;

    //Update post
    if(!$category->update()){
        echo json_encode(array('message' => 'Category Not Updated'));
    }
}