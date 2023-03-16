<?php
/*//Header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
*/
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();

//Instantiate quote object
$quote = new Quote($db);

//Get ID
$quote->id = isset($_GET['id']) ? $_GET['id'] : die();


//Get quote
$quote->seeSingleQuote();
//print_r($quote->author_id);
/* //Create array
$quote_arr = array(
    'id' => $quote->id, 
    'quote' => $quote->quote,
    'author_id' => $quote->author_id,
    'category_id' => $quote->category_id); */

//Make JSON
if($quote->quote === false){
    echo json_encode(
        array('message' => 'No Quotes found'));
}
else{
    print_r(json_encode($quote->quote));
}