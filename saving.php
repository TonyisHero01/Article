<?php
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);
$name = $input["name"] ?? "";
$content = $input["content"] ?? "";

$article = new Article($page["id"], $name, $content);
try {
    $database->edit($article);
    echo "edit";
}
catch (MissingIdException $mie) {
    echo "404";
    http_response_code(404);
} 
catch (WrongFormatException $wfe) {
    echo "400";
    http_response_code(400);
}