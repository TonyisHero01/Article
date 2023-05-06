<!DOCTYPE html>
<html>
<head>
    <title>article list</title>
    <link rel="stylesheet" type="text/css" href="<?php echo APP_DIRECTORY ?>style.css">
</head>
<body>
    <h1>Article list</h1>
    <table>
        <?php
            $articles = $database->readAll();
            $article_ids = [];
            foreach ($articles as $article) {
                $article_ids[] = $article->id;
                ?>
                <tr id="<?php echo $article->id?>">
                    <td class="article-name"><?php echo $article->name?></td>
                    <td class="button"><span class="show-button" type="button" onclick="show(<?php echo $article->id?>)">Show</span></td>
                    <td class="button"><span class="edit-button" type="button" onclick="edit(<?php echo $article->id?>)">Edit</span></td>
                    <td class="button"><span class="delete-button" type="button" onclick="delete_(<?php echo $article->id?>)">Delete</span></td>
                </tr>
            <?php
            }
        ?>
    </table>
    <div class="buttons">
        <button id="previousPageButton" type="button" onclick="previousPage()">Previous</button>
        <button id="nextPageButton" type="button" onclick="nextPage()">Next</button>
        <span id="pageCount">Page Count </span>
        <button class="popup" onclick="openCreateForm()">Create Article</button>
    </div>

    <div class="popuptext" id="myPopup" style="display: none;">
        <form action="#">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required maxlength="<?php echo NAME_MAX_LENGTH?>" oninput="enableCreateButton()"><br>
            <button type="button" id="createButton" onclick="create()" disabled>Create</button>
            <button id="cancelButton" type="button" onclick="cancelCreateForm()" style="display: none;">Cancel</button>
        </form>
    </div>

    <script>
        var pageNumber = 0;
        var pageCountText = document.getElementById("pageCount");
        var ids = [<?php 
            foreach($article_ids as $a_id) {
                echo $a_id . ",";
            }
            ?>];
    
        var previousPageButton = document.getElementById("previousPageButton");
        var nextPageButton = document.getElementById("nextPageButton");
        
        
        showPage();
        function nextPage() {
            pageNumber++;
            showPage();
        }
        function previousPage() {
            pageNumber--;
            showPage();
        }
        
        function showPage() {
            var lastPageNumber = Math.trunc((ids.length-1) / <?php echo MAX_ARTICLES_COUNT_PER_PAGE?>);
            pageCountText.textContent = "Page Count " + (lastPageNumber+1);
            if (pageNumber == lastPageNumber+1) {
                pageNumber--;
            }
            let start = <?php echo MAX_ARTICLES_COUNT_PER_PAGE?> * pageNumber;
            let end = start + <?php echo MAX_ARTICLES_COUNT_PER_PAGE?>;
            for (let i = 0; i < ids.length; i++) {
                let tableRow = document.getElementById(ids[i]);
                if (i >= start && i < end) {
                    //tableRow.removeAttribute("style");
                    tableRow.style.removeProperty("display");
                }
                else {
                    //tableRow.setAttribute("style", "display: none;");
                    tableRow.style.display = "none";
                }
            }
            if (pageNumber == 0) {
                previousPageButton.style.visibility = "hidden";
            }
            else {
                previousPageButton.style.visibility = "visible";
            }
            if (pageNumber == lastPageNumber) {
                nextPageButton.style.visibility = "hidden";
            }
            else {
                nextPageButton.style.visibility = "visible";
            }
        }
        var popup = document.getElementById("myPopup");
        var cancelButton = document.getElementById("cancelButton");
        function openCreateForm() {
            popup.removeAttribute("style");
            cancelButton.removeAttribute("style");
        }
        
        function cancelCreateForm() {
            console.log("cancel");
            popup.setAttribute("style", "display: none;");
            cancelButton.setAttribute("style", "display: none;");
            //window.location.href = "<?php echo APP_DIRECTORY?>articles;
        }
        var nameElement = document.getElementById("name");
        function enableCreateButton () {
            console.log("called func");
            
            var submitElement = document.getElementById("createButton");
            if (nameElement.value != "" && nameElement.value.length <= 32) {
                submitElement.removeAttribute("disabled");
            }
            else {
                submitElement.setAttribute("disabled", "disabled");
            }
        }
        async function create() {
            console.log("funguje create");
            var response = await fetch("<?php echo APP_DIRECTORY?>article-create/",{
                method: "POST",
                headers: {
                    'content-type' : 'application/json'
                },
                body: JSON.stringify({"name" : nameElement.value})
            });
            //console.log(await response.text());
            var id = (await response.json())["id"];
            //console.log(id);
            //{id:1}
            window.location.href = "<?php echo APP_DIRECTORY?>article-edit/" + id;
        }
        function show(id) {
            window.location.href = "<?php echo APP_DIRECTORY?>article/" + id;
        }
        function edit(id) {
            window.location.href = "<?php echo APP_DIRECTORY?>article-edit/" + id; // window.location.href = "/~76824974/cms/article-edit/" + id;
        }
        function delete_(id) {
            var response = fetch("<?php echo APP_DIRECTORY?>article-delete/" + id, {
                method: "DELETE"
            });
            var row = document.getElementById(id);
            row.remove();
            ids.splice(ids.indexOf(id), 1);
            showPage();
        }
    </script>

</body>
</html>