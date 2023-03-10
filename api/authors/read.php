<?php
/*//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();*/

//Instantiate author object
$author = new Author($db);

//Blog post query
$result = $author->read();
//Get row count
$num = $result->rowCount();

// Check if any posts
if($num > 0){
	//Author array
	$author_arr = array();
	$author_arr['data'] = array();

	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		extract($row);

		$author_item = array('id' => $id, 'author' => $author);

		//Push to "data"
		array_push($author_arr['data'], $author_item);
	}

	//Turn to JSON & output
	echo json_encode($author_arr);
}else{
	echo json_encode(
	array('message' => 'No Authors found'));
}