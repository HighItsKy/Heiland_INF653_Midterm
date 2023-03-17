<?php
/*
//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
*/
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();

//Instantiate category object
$category = new Category($db);

//Category query
$result = $category->seeCategories();
//Get row count
$num = $result->rowCount();

// Check if any posts
if($num > 0){
	//Category array
	$category_arr = array();
	$category_arr['data'] = array();

	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		extract($row);

		$category_item = array('id' => $id, 'category' => $category);

		//Push to "data"
		array_push($category_arr['data'], $category_item);
	}

	//Turn to JSON & output
	echo json_encode($category_arr['data']);
}else{
	echo json_encode(
	array('message' => 'No Categories found'));
}