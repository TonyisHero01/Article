<?php
// db = new Database($conn)
class Database {
    public $tableName;
    private $conn;
    function __construct($tableName, $conn)
    {
        $this->tableName = $tableName;
        $this->conn = $conn;
    }
    function contains($id): bool {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $this->tableName WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_row()[0] == 1;
        $stmt->close();
        return $exists;
    }
    public function utm_contains($utm_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $this->tableName WHERE utm_id = ?");
        $stmt->bind_param("s", $utm_id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_row()[0] == 1;
        $stmt->close();
        return $exists;
    }
    public function utm_contains_article($article_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $this->tableName WHERE article_id = ?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_row()[0] >= 1;
        $stmt->close();
        return $exists;
    }
    public function readAll() {
        $query = $this->conn->query("SELECT id, name FROM $this->tableName ORDER BY id DESC");
        while($row = $query->fetch_row()) {
            [$id, $name] = $row;
            yield new Article($id, $name, null);
        }
    }
    public function utm_readAll() {
        $query = $this->conn->query("SELECT utm_id, counter FROM $this->tableName");
        while($row = $query->fetch_row()) {
            [$utm_id, $counter] = $row;
            yield new UTM_Article($utm_id, $counter);
        }
    }
    public function read($id): Article {
        if (!$this->contains($id)) {
            throw new MissingIdException();
        }
        $stmt = $this->conn->prepare("SELECT name, content FROM $this->tableName WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        [$name, $content] = $stmt->get_result()->fetch_row();
        $stmt->close();
        return new Article($id, $name, $content);
    }
    public function utm_readCounter($utm_id) {
        if (!$this->utm_contains($utm_id)) {
            throw new MissingIdException();
        }
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName WHERE utm_id = ?");
        $stmt->bind_param("i", $utm_id);
        $stmt->execute();
        [$id, $utm_id, $counter] = $stmt->get_result()->fetch_row();
        $stmt->close();
        return $counter;
    }
    public function create($name): int {
        if ($name == "" || strlen($name) > NAME_MAX_LENGTH) {
            throw new WrongFormatException();
        }
        $name = htmlspecialchars($name);
        $stmt = $this->conn->prepare("INSERT INTO $this->tableName (name, content) VALUES (?, \"\")");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();

        $query = "SELECT MAX(id) FROM $this->tableName";
        $result = $this->conn->query($query);
        return $result->fetch_row()[0];
    }
    public function insert_UTM($article_id, $utm_id) {
        if (strlen($utm_id) > 16) {
            throw new WrongFormatException();
        }
        $stmt = $this->conn->prepare("INSERT INTO UTM_SOURCE (article_id, utm_id, counter) VALUES (?, ?, 1)");
        $stmt->bind_param("is", $article_id, $utm_id);
        $stmt->execute();
        $stmt->close();
    }
    public function edit($article) {
        //true false
        if (!$this->contains($article->id)) {
            throw new MissingIdException();
        }
        elseif ($article->name == "" || strlen($article->name) > NAME_MAX_LENGTH || strlen($article->content) > CONTENT_MAX_LENGTH) {
            throw new WrongFormatException();
        }
        $name = htmlspecialchars($article->name);
        $content = htmlspecialchars($article->content);
        $stmt = $this->conn->prepare("UPDATE $this->tableName SET name = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $content, $article->id);
        $stmt->execute();
        $stmt->close();
    }
    public function utm_update_counter($counter, $utm_id) {
        if (!$this->utm_contains($utm_id)) {
            throw new MissingIdException();
        }
        $stmt = $this->conn->prepare("UPDATE $this->tableName SET counter = ? WHERE utm_id = ?");
        $counter = $counter + 1;
        $stmt->bind_param("is", $counter, $utm_id);
        $stmt->execute();
        $stmt->close();
    }
    public function delete($id) {
        if (!$this->contains($id)) {
            throw new MissingIdException();
        }
        $stmt = $this->conn->prepare("DELETE FROM $this->tableName WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
/*
class Article {
    public $id;
    public $name;
    public $content;
    
}
*/
enum RequestStatus {
    case MISSING_ID;
    case WRONG_FORMAT;
    case OK;
}
class MissingIdException extends Exception {
    
}
class WrongFormatException extends Exception {

}











/*
$idFromForm;
$nameFromForm;
$contentFromForm;

require_once "db_config.php";

$conn  = new mysqli($db_config["server"], $db_config["login"], $db_config["password"], $db_config["database"]);
if ($conn->connect_error) {
    print("Cannot connect to Database!");
    exit();
}

$db = new Database("articles_for_zp", $conn);

echo "connect successfull"."<br>";
 
$stmt->close();

if ($exists) {
    $stmt = $conn->prepare("UPDATE articles_for_zp SET name = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nameFromForm, $contentFromForm, $idFromForm);
}
else {
    $stmt = $conn->prepare("INSERT INTO articles_for_zp (id, name, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $idFromForm, $nameFromForm, $contentFromForm);
}
$stmt->execute();
$stmt->close();

$conn->close();
*/
