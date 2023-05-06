<?php 
//echo "hello1111";
//var_dump($_GET);
require_once "db_config.php";
require_once "./constants.php";
require_once "editing.php";
define("ACTIONS_WITH_ID", ["article", "article-edit", "article-save", "article-delete", "article-create"]);
$page = parsePage($_GET["page"]);

/*
echo "hello2222";
var_dump($_SERVER);
echo "hello3333";
*/

$conn = new mysqli($db_config["server"], $db_config["login"], $db_config["password"], $db_config["database"]);

if ($conn->connect_error) {
    print("Cannot connect to Database!");
    exit();
}
$database = new Database("articles_for_zp", $conn);


switch ($page["action"]) {
    case "article": 
        /*
        $utm_id = $_GET["utm_source"];
    if (!is_null($utm_id)) {
        $counter = 0;
        echo "utm_source: " . $utm_id;
    }
    */
        require_once "./article.php";
        break;
    case "articles": 
        require_once "./articles.php";
        break;
    case "article-edit":
        require_once "./article-edit.php";
        break;
    case "article-save":
        require_once "./saving.php";
        break;
    case "article-delete": 
        require_once "./deleting.php";
        break;
    case "article-create":
        require_once "./creating.php";
        break;
}

function parsePage($page) {
    $partsOfPage = explode("/", $page);
    $action = $partsOfPage[0];
    $result = [];
    if (in_array($action, ACTIONS_WITH_ID)) {
        $id = intval($partsOfPage[1] ?? null);
        if ($id <= 0) {
            $id = null;
        }
    }
    else {
        if ($action != "articles") {
            $action = null;
        }
        $id = null;
    }
    $result = ["action" => $action, "id" => $id];
    return $result;
}
//["article", 1]
//["articles", null]
//["article", null]
//[null, null]



class Article {
    public $id;
    public $name;
    public $content;
    function __construct($id, $name, $content) {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
    }
}
class UTM_Article {
    public $utm_id;
    public $counter;
    function __construct($utm_id, $counter) {
        $this->utm_id = $utm_id;
        $this->counter = $counter;
    }
}
?>
