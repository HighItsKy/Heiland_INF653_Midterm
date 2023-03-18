<?php
//Header
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

//Instantiate author object
$author = new Author($db);

//Get ID
$author->id = isset($_GET['id']) ? $_GET['id'] :  die();

//Get author
$author->seeSingleAuthor();

if($author->author === false){ //If no author is in the table
    echo json_encode(array('message' => 'author_id Not Found'));
}
else{
    //Make JSON
    print_r(json_encode($author->author));
}