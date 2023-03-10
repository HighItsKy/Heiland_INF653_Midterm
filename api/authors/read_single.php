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

//Get ID
$author->id = isset($_GET['id']) ? $_GET['id'] : die();

//Get author
$author->read_single();

//Create array
$author_arr = array(
    'id' => $author->id, 
    'author' => $author->author);

//Make JSON
print_r(json_encode($author_arr));