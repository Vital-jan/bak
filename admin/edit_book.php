<!DOCTYPE html>
<html lang="en">
<?
    require '../php_ajax/connect.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../assets/admin.css">
    <script src='<?=PHP_PATH?>mysqlajax.js'></script>
    <title>Admin page</title>
</head>

<?
    $query = mysql_query("SELECT books.book_id, books.book, books.describe, books.folder, books.modified, books.picture, books.available, books.price, folders.folder_id AS folder_id, folders.folder AS folder_folder FROM books LEFT JOIN folders ON books.folder = folders.folder_id order by modified desc,folder_folder, books.book");

    $books = array(); // книги
    while ($cRecord = mysql_fetch_assoc($query)) {
        $cRecord['book'] = strip_tags($cRecord['book']);
        $cRecord['describe'] = strip_tags($cRecord['describe']);
        $books[] = $cRecord;
    }

    // розділи книг
    $query = mysql_query("SELECT folders.folder_id as 'id', folders.folder as 'value' FROM folders order by folders.folder");
    $folders = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        $folders[] = $cRecord;
    }

    // автори книг
    $query = mysql_query("SELECT bookauthor.bookauthor_id, bookauthor.book,  authors.author FROM bookauthor LEFT JOIN authors on bookauthor.author = authors.author_id order by bookauthor.book");
    $bookauthors = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        $bookauthors[] = $cRecord;
    }
    // function bookauthors_assemble($books, $bookauthors){
    for ($n=0; $n < count($books); $n++) {
        foreach($bookauthors as $key=>$value) {
            if ($books[$n]['book_id'] == $value['book']) $books[$n]['assemble'] .= $value['author'].', ';
        }
        $books[$n]['assemble'] = substr($books[$n]['assemble'], 0, strlen($books[$n]['assemble']) - 2);
    }    
?>

<body class="table">
<header>
    <div id="modal-back"></div> <!--підкладка модального вікна (невидима) -->
    <ul>
        <li class='row row-header'>
            <button type="button">Додати книгу</button>
            <button type="button">Поновити</button>
            <button type="button">Вийти</button>
        </li>
        <li class='row'>
            <span class='column column-header'></span>
            <span class='column column-header'>Книга</span>
            <span class='column column-header'>Автор</span>
            <span class='column column-header'>Розділ</span>
            <span class='column column-header'>Змінено</span>
        </li>
    </ul>
</header>
    <form id="form-edit" name="edit">
        <ul>
            <li><input type="text" name="book" placeholder="Назва книги"><input style="width:10%" type="text" name="id" disabled=''></li>
            <li><textarea name="describe" placeholder="Опис книги" rows="8"></textarea></li>
            <li>Зображення:<input type="text" name="picture" placeholder="Файл зображення" disabled="">
            <button type="button" id="file-btn">...</button>  
            <button type="button" id="upload-btn">Завантажити</button>            </li>
            <li>Ціна:<input type="text" name="price" placeholder="Ціна"></li>
            Наявність:
            <select name="available">
                <option value="" id="available"></option>
                <option value="0" id="available">Ні</option>
                <option value="1" id="available">Так</option>
            </select>
            <li>Розділ:
                <select name="folder" id="folder">
                </select>
            </li>
            <li>Автори:</li>
        </ul>
        <ul id="bookauthor"></ul>
        <button id='author-add' type="button">Додати автора</button>
        <ul id="bookauthor-select"></ul>
        <br><br>
        <div class="form-photo"></div>
        <button type="button" id="save">Save</button>
        <button type="button" id="close">Close</button>
        <button type="button" id="undo">Undo</button>
    </form>
<?
$n = 0;
echo "<ul>";
foreach($books as $key=>$value) {
            echo "
                <li class='row' id='array-item{$n}'>
                <span class='column'><button id='edit' data-number='{$n}'></button></span>
                <span class='column'>{$value['book']}</span>
                <span class='column'>{$value['assemble']}</span>
                <span class='column'>{$value['folder_folder']}</span>
                <span class='column'>{$value['modified']}</span>
                </li>
            ";
            $n++;
        }
    echo "</ul>";
?>

    <script>
        let currentRecord;
        let form = document.querySelector('form');
        let books = <?=json_encode($books)?>;
        books.forEach((i)=>{i.change = false});
        let authors = <?=json_encode($authors)?>;
        let folders = <?=json_encode($folders)?>;
        let bookAuthor = <?=json_encode($bookauthor)?>;
        selectCreate('folder', folders);
        let formEdit = document.forms.edit.elements;
        let modalBack = document.querySelector('#modal-back');

        document.querySelector('#bookauthor-select').addEventListener('mouseleave', (event)=>{// Обробник закриття вікна додавання авторів
                    document.querySelector('#bookauthor-select').style.display = 'none';
                });

        document.querySelector('#bookauthor-select').addEventListener('click', (event)=>{// Обробник кліка по автору (додає автора до книги)
                if (event.target.id == 'author-insert') {
                    document.querySelector('#bookauthor-select').style.display = 'none';
                    queryInsert('bookauthor', [
                        ['#book', `${books[currentRecord].book_id}`],
                        ['#author', `${event.target.dataset.id}`]
                    ], refreshAuthors, '<?=PHP_PATH?>');
                }
                function refreshAuthors(resolve){
                    bookauthorCreate();
                }
            });

        // кнопка Додати автора (відкриває вікно додавання автору)
        document.querySelector('#author-add').addEventListener('click', (event)=>{
            queryGet('select * from authors order by author', action, '<?=PHP_PATH?>');
            let s = document.querySelector('#bookauthor-select');
            s.style.display = 'block';
            
            function action(response){
                s.innerHTML = '';
                response.forEach((i)=>{
                    s.innerHTML += `<li id='author-insert' data-id='${i.author_id}'>${i.author}</li>`;
                })
            }
        })

        // кнопка close (форма)
        document.querySelector('form button#close').addEventListener('click', (event)=>{
            modalBack.style.display = 'none';
            event.target.parentElement.style.display = 'none';
        });

        // Кнопка save (форма)
        document.querySelector('form button#save').addEventListener('click', (event)=>{
            // перевірка значень полів форми ......
            // {}
            event.target.parentElement.style.display = 'none';
            modalBack.style.display = 'none';

            queryUpdate('books', `book_id = ${books[currentRecord].book_id}`, [
                ['book', formEdit.book.value],
                ['describe', formEdit.describe.value],
                ['picture', formEdit.picture.value],
                ['#price', formEdit.price.value],
                ['available', formEdit.available.value],
                ['#folder', formEdit.folder.value]
            ], 
            updateBook, '<?=PHP_PATH?>');

            function updateBook(response){
                if (!response.sql) alert('Помилка! Інформація не збережена.'+response.query+response.error); else {
                    books[currentRecord].book = formEdit.book.value;
                    books[currentRecord].describe = formEdit.describe.value;
                    books[currentRecord].picture = formEdit.picture.value;
                    books[currentRecord].price = formEdit.price.value;
                    books[currentRecord].available = formEdit.available.value;
                    books[currentRecord].folder = formEdit.folder.value;
                    books[currentRecord].change = true;
                    let editElem = document.querySelector(`#array-item${currentRecord}`);
                    editElem.style.color = 'blue';
                    editElem.setAttribute('title','Цей елемент був змінений. Щоб побачити зміни слід поновити сторінку');
                }
            }
            }); // 'form button#save').addEventListener

        // Кнопка undo (форма)
        document.querySelector('form button#undo').addEventListener('click', (event)=>{
            refresh(currentRecord);
        });

        function selectCreate(id, itemList, nullAllowed = false){ // створення списку елементів select
            let el = document.querySelector(`#${id}`);
            if (nullAllowed) el.innerHTML = `<option id='${id}' value = ""></option>`;
            itemList.forEach((i, n)=>{
                el.innerHTML += `<option id='${id}' value = '${i.id}'>${i.value}</option>`;
            })
        }
        function selectRefresh (id, value){ // оновлення ел-тів select.
            let itemList = document.querySelectorAll(`select option#${id}`);
            itemList.forEach((i, n)=> {
                i.removeAttribute('selected');
                if (i.value == value) i.setAttribute('selected',''); 
                if (i.value == '' && value == null) i.setAttribute('selected','');
            });
        }
        function bookauthorCreate(){ // створити перелік авторів поточної книги
            queryGet('SELECT bookauthor.bookauthor_id, bookauthor.book, authors.author FROM bookauthor LEFT JOIN authors on bookauthor.author = authors.author_id', bookauthorResolve, '<?=PHP_PATH?>');
            function bookauthorResolve(resolve) {
            let s = '';
            resolve.forEach((i)=>{
                if (i.book == books[currentRecord].book_id) s += `<li><img src='../assets/img/close.png' title='Видалити' id='del-author' data-id='${i.bookauthor_id}'>${i.author}</li>`;
            })
            document.querySelector('form #bookauthor').innerHTML = s;

            }
        };
        
        document.querySelector('form #bookauthor').addEventListener('click', (event)=>{ // обробка кліка delete на переліку авторів поточної книги
            if (event.target.id == 'del-author') {
                queryDelete('bookauthor', `bookauthor.bookauthor_id=${event.target.dataset.id}`, (response)=>{
                    bookauthorCreate()
                },
                '<?=PHP_PATH?>');
            }
        })

        function refresh(num) { // прив'язка значень полів до ел-тів форми
            if (isNaN(num)) return;
            currentRecord = num;
            form.id.value = books[num].book_id;
            form.book.value = books[num].book;
            form.describe.value = books[num].describe;
            form.picture.value = books[num].picture;
            form.price.value = books[num].price;
            selectRefresh('available', books[num].available);
            selectRefresh('folder', books[num].folder);
            document.querySelector('form .form-photo').innerHTML = `<img src='../assets/img/books/${books[num].picture}'>`;
            bookauthorCreate();
        }

        document.body.addEventListener('click', (event)=>{ // редагування запису
            if (event.target.id == 'edit') {
                modalBack.style.display = 'block';
                form.style.display = 'block'; // показати форму

                let num = event.target.dataset.number; // отримати номер елементу
                refresh(num); // прив'язати значення полів до елементів
            };
        });
    </script>
</body>
</html>