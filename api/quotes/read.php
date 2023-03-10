<?php
/*
//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();*/

//Instantiate quote object
$quote = new Quote($db);

//Quote query
$result = $quote->read();
//Get row count
$num = $result->rowCount();

// Check if any posts
if($num > 0){
	//Quote array
	$quote_arr = array();
	$quote_arr['data'] = array();

	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		extract($row);

		$quote_item = array('id' => $id, 'quote' => $quote, 'author_id' => $author_id, 'category_id' => $category_id);

		//Push to "data"
		array_push($quote_arr['data'], $quote_item);
	}

	//Turn to JSON & output
	echo json_encode($quote_arr);
}else{
	echo json_encode(
	array('message' => 'No Quotes found'));
}