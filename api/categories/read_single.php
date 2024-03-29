<?php
//Header
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();

//Instantiate category object
$category = new Category($db);

//Get ID
$category->id = isset($_GET['id']) ? $_GET['id'] : die();

//Get category
$category->seeSingleCategory();

//Create array
if($category->category === false){ //If no category is in the table
    echo json_encode(array('message' => 'category_id Not Found'));
}
else{
    //Make JSON
    print_r(json_encode($category->category));
}