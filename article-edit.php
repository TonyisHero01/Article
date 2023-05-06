<?php
try {
    $article = $database->read($page["id"]);
} catch (MissingIdException $mie) {
    http_response_code(404);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>article edit</title>
    <link rel="stylesheet" type="text/css" href="<?php echo APP_DIRECTORY ?>style.css">
</head>
<body>
    <div id="body">
    <form action="#">
        <label for="name">Name</label><br>
        <input type="text" id="name" name="name" required maxlength="<?php echo NAME_MAX_LENGTH?>" oninput="enableCreateButton()" value="<?php echo $article->name?>"><br>
        <label for="content">Content</label><br>
        <textarea id="content" name="content" maxlength="1024" oninput="enableCreateButton()" rows="15"><?php echo $article->content?></textarea><br>
        <div class="buttons">
        <button type="button" id="saveButton" onclick="save_()" disabled="">Save</button>
        <button type="button" id="backButton" onclick="backToArticles()">Back to articles</button>
        </div>
    </form>
    </div>
<script>    
    console.log("tadyta stranka");
    var nameElement = document.getElementById("name");
    var contentElement = document.getElementById("content");
    
    function enableCreateButton() {
        console.log("called func");
        
        var submitElement = document.getElementById("saveButton");
        //console.log("pred if");
        if (nameElement.value != "" && nameElement.value.length <= <?php echo NAME_MAX_LENGTH?> && contentElement.value.length <= <?php echo CONTENT_MAX_LENGTH?>) {
            console.log("v podmince");
            submitElement.removeAttribute("disabled");
        }
        else {
            console.log("v else");
            submitElement.setAttribute("disabled", "disabled");
        }
    }
    //https://blog.51cto.com/zhezhebie/5445075 - can't name function as save()
    async function save_() {
        await fetch('<?php echo APP_DIRECTORY ?>article-save/<?php echo $page["id"]?>', {
            method: "POST",
            headers: {
                "content-type" : "application/json"
            },
            body: JSON.stringify({"name" : nameElement.value, "content" : contentElement.value})
        });
        window.location.href = "<?php echo APP_DIRECTORY ?>articles";
                                        // "/~76824974/cms/articles"
    }
    function backToArticles() {
        window.location.href = "<?php echo APP_DIRECTORY ?>articles";
    }
</script>

</body>
</html>