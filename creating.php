<?php
require_once "./constants.php";
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);
$name = $input["name"];
$content = "";
try {
    $id = $database->create($name);
} catch (WrongFormatException $wfe) {
    http_response_code(400);
}
echo json_encode(["id" => $id]);