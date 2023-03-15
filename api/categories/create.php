<?php
/*
//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');*/
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();

//Instantiate category object
$category = new Category($db);

//Get raw data
$data = json_decode(file_get_contents("php://input"));

if(!get_object_vars($data) || !isset($data->category)){ //If there are no parameters
    echo json_encode(array('message' => 'Missing Required Parameters'));
}
else{
    $category->category = $data->category;

    //Create category
    if($category->create()){
        echo json_encode(array('message' => 'Category Created'));
    }
    else{
        echo json_encode(array('message' => 'Category Not Created'));
    }
}