<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../assets/admin.css">
    <title>Admin page</title>
</head>

<?
    require '../template/connect.php';
    mysql_select_db(DB_NAME, mysql_connect(DB_HOST, DB_USER, DB_PASS));

    $query = mysql_query("SELECT books.book_id, books.book, books.describe, books.folder, books.modified, books.picture, books.available, books.price, folders.folder_id AS folder_id, folders.folder AS folder_folder FROM books LEFT JOIN folders ON books.folder = folders.folder_id");

    $books = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        $cRecord['describe'] = strip_tags($cRecord['describe'], '<br>');
        $books[] = $cRecord;
}

// foreach($books as $key=>$value){
//     $upd = mysql_query(
//        "UPDATE books SET books.picture = '{$value['picture']}' WHERE books.book_id = {$value['book_id']}"
//     );
// }

// розділи книг
$query = mysql_query("SELECT * FROM folders");
$folders = array();
while ($cRecord = mysql_fetch_assoc($query)) {
    $folders[] = $cRecord;
}

// автори книг
$query = mysql_query("SELECT * FROM authors");
$authors = array();
while ($cRecord = mysql_fetch_assoc($query)) {
    $authors[] = $cRecord;
}

    
?>

<body class="table">
    <form id="form-edit">
        <ul>
            <li><input type="text" name="book" placeholder="Назва книги"></li>
            <li><textarea name="describe" placeholder="Опис книги" rows="8"></textarea></li>
            <li><input type="text" name="picture" placeholder="Файл зображення" disabled="">
            <button type="button" id="file-btn">...</button>  
            <button type="button" id="upload-btn">Завантажити</button>            </li>
            <li><input type="text" name="price" placeholder="Ціна"></li>
            <li><input type="text" name="available" placeholder="Наявність"></li>
            <li><input type="text" name="author"><button type="button" id="author-btn">...</button></li>
            <li><input type="text" name="folder"><button type="button" id="folder-btn">...</button></li>
            <div class="form-photo"></div>
        </ul>
        <button type="button" id="save">Save</button>
        <button type="button" id="close">Close</button>
    </form>
    <?
    echo "
    <ul>
    <li class='row row-header'>
    <span class='book'>Книга</span>
    <span class='describe'>Опис</span>
    <span class='folder'>Розділ</span>
    </li>
    ";
$n = 0;
foreach($books as $key=>$value) {
            $d = strip_tags($value['describe']);
            echo "
                <li class='row'>
                <span class='book'><button id='edit' data-number='{$n}'></button>{$value['book']}</span>
                <span class='describe'>{$d}</span>
                <span class='folder'>{$value['folder_folder']}</span>
                </li>
            ";
            $n++;
        }
    echo "</ul>";
    ?>

    <script>
        let books = <?=json_encode($books)?>;
        let authors = <?=json_encode($authors)?>;
        let folders = <?=json_encode($folders)?>;

        // кнопка close (форма)
        document.querySelector('form button#close').addEventListener('click', (event)=>{
            event.target.parentElement.style.display = 'none';
        });
        // Кнопка save (форма)
        document.querySelector('form button#save').addEventListener('click', (event)=>{
            event.target.parentElement.style.display = 'none';
        });

        document.body.addEventListener('click', (event)=>{
            if (event.target.id == 'edit') {
                let form = document.querySelector('form');
                form.style.display = 'block';
                form.style.top = window.pageYOffset+'px';
                form.book.value = books[event.target.dataset.number].book;
                form.describe.value = books[event.target.dataset.number].describe;
                form.picture.value = books[event.target.dataset.number].picture;
                form.price.value = books[event.target.dataset.number].price;
                form.available.value = books[event.target.dataset.number].available;
                form.author.value = books[event.target.dataset.number].author;
                form.folder.value = books[event.target.dataset.number].folder;
                document.querySelector('form .form-photo').innerHTML = `<img src='../assets/img/books/${books[event.target.dataset.number].picture}'>`;
            };
        });
    </script>
</body>
</html>