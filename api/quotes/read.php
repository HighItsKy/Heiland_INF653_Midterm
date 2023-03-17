<?php
/*
//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
*/
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();

//Instantiate quote object
$quote = new Quote($db);

//Quote query
$result = $quote->seeQuotes();
//Get row count
$num = $result->rowCount();

// Check if any quotes
if($num > 0){
	//Quote array
	$quote_arr = array();
	$quote_arr['data'] = array();

	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		extract($row);

		//$quote_item = array('id' => $id, 'quote' => $quote, 'author_id' => $author_id, 'category_id' => $category_id);
		$quote_item = array('id' => $id, 'quote' => $quote, 'author' => $author, 'category' => $category);

		if(isset($_GET['category_id']) && isset($_GET['author_id'])){ //If there is a category ID and an author ID in the url, 
			if($_GET['category_id'] == $category_id && $_GET['author_id'] == $author_id){ //all quotes with the matching author AND category is outputted.
				//Then push to "data"
				array_push($quote_arr['data'], $quote_item);
			}
		}
		else if(isset($_GET['author_id'])){ //If there is an author ID in the URL, 
			if($_GET['author_id'] == $author_id){ //all entries with that author ID is outputted.
				//Then Push to "data"
				array_push($quote_arr['data'], $quote_item);
			}
		}
		else if(isset($_GET['category_id'])){ //If there is a category ID in the URL, 
			if($_GET['category_id'] == $category_id){ //all entries with that ID are outputted. 
				//Then push to "data"
				array_push($quote_arr['data'], $quote_item);
			}
		}
		else if(!isset($_GET['author_id']) && !isset($_GET['category_id'])){ //If there is no parameters in the URL, all quotes are outputted.
			//Push to "data"
			array_push($quote_arr['data'], $quote_item);
		} 
	}

	//Count how many quotes there are that were requested
	$count = sizeof($quote_arr['data']);
	//echo($count);

	if($count > 0){ //If there is at least one quote that matched what was being requested
		echo json_encode($quote_arr['data']);	
	}
	else{
		echo json_encode(
			array('message' => 'No Quotes found'));
	}
	
	
}else{
	echo json_encode(
	array('message' => 'No Quotes found'));
}