<?php
//Header
/*header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');*/
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

//Instantiate author object
$author = new Author($db);

//Get raw author data
$data = json_decode(file_get_contents("php://input"));

$author->id = $data->id;

//Create author
if($author->delete())
{
    echo json_encode(array('message' => 'Author Deleted'));
}
else
{
    echo json_encode(array('message' => 'Author Not Deleted'));
}