<?php 
    $id = $page["id"];
    try {
        $article = $database->read($id);
    }
    catch (MissingIdException $mie){
        http_response_code(404);
    }
    $database_UTM = new Database('UTM_SOURCE', $conn);
    
    if (is_array($_GET) && count($_GET)>0) {
        if (isset($_GET["utm_source"])) {
            $utm_id = $_GET["utm_source"];
            if (!$database_UTM->utm_contains($utm_id)) {
                $database_UTM->insert_UTM($id, $utm_id);
            }
            else {
                $counter = $database_UTM->utm_readCounter($utm_id);
                $database_UTM->utm_update_counter($counter, $utm_id);
            }
            $counter = $database_UTM->utm_readCounter($utm_id);
        }
    }
    
    

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $article->name?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo APP_DIRECTORY ?>style.css">
</head>
<body>
<?php
echo "<h1 id=\"article_name\">" . $article->name . "</h1>";
echo "<p>" . $article->content . "</P>";
?>
<?php if ($database_UTM->utm_contains_article($id)) { ?>
    <script>
        var utmIdsValues = [];
    </script>
    <table>
        <?php
            $utm_articles = $database_UTM->utm_readAll();
            foreach ($utm_articles as $article) {
                ?>
                <tr id = "<?php echo $article->utm_id?>">
                    <td class="utm_id"><?php echo $article->utm_id?></td>
                    <td class="counter"><?php echo $article->counter?></td>
                </tr>
                
            <?php    
            } 
        ?>
    </table>
    <form>
        <input type="text" id="utmInput"><br>
        <button id="utmButton" type="button" onclick="generateUrl()">Generate</button>
    </form>
    <textarea id="textarea" style="display: none;"></textarea>
    <script>
        var utmIds = document.getElementsByClassName("utm_id");
        var url = window.location.href; //https://webik.ms.mff.cuni.cz/~76824974/cms/article/35
        function generateUrl() {
            var utm = document.getElementById("utmInput");
            var link = url + "?utm_source=" + utm.value;
            //var htmltext = "<textarea style=\"width: max;\" disabled=\"disabled\"></textarea>";
            var textarea = document.getElementById("textarea");
            textarea.innerHTML = "<a href=" + link + ">" + document.getElementById("article_name").innerHTML + "</a>";
            textarea.removeAttribute("style");
            console.log(htmltext);
            document.write(htmltext);
        }
        /*
        var link = "https://webik.ms.mff.cuni.cz/cms/article/" + 1sd349?utm_source=007";
        var str = "<a href="">My Article</a>";*/
    </script>
<?php } ?>
<div class="buttons">
    <button type="button" id="editButton" onclick="window.location.href = '<?php echo APP_DIRECTORY ?>article-edit/<?php echo $id?>'">Edit</button>
    <button type="button" onclick="window.location.href = '<?php echo APP_DIRECTORY ?>articles'">Back to articles</button>
</div>

</body>