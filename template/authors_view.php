<!DOCTYPE html>
<?
    $current_author = $_GET['author'];
    $photo_folder = BOOK_PHOTO_FOLDER;
    $login = getLogin();

    // завантажуємо з бд авторів
    $where = $current_author ? "where author_id={$current_author}":'';
    
    $selectauthors = "select `authors`.author as authorname, `authors`.author_id, `authors`.describe, `authors`.photo, sel3.cnt from authors left join 
    (select author, count(*) as cnt from (select distinct author, book from bookauthor) as sel2 group by author) as sel3
    on `authors`.author_id = sel3.author {$where} order by cnt desc";

    $query = mysql_query($selectauthors);
    $authors = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        if ($cRecord['author_id'] == $current_author) array_unshift($authors, $cRecord); else { // обраного автора на перше місце
            $authors[] = $cRecord;
        }
    }

    // завантажуємо книги авторів
    $query = mysql_query("SELECT DISTINCT
    bookauthor.book,
    books.book,
    books.book_id,
    bookauthor.author AS authorID,
    books.`describe`,
    books.price,
    books.available,
    books.picture,
    folders.folder,
    folders.folder_id
    FROM bookauthor
        LEFT JOIN books ON bookauthor.book = books.book_id
            LEFT JOIN folders ON folders.folder_id = books.folder
    ");

    $books = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $books[] = $cRecord;
    }

    // завантажуємо авторів книг
    $query = mysql_query("SELECT bookauthor.bookauthor_id, bookauthor.book,  authors.author FROM bookauthor LEFT JOIN authors on bookauthor.author = authors.author_id order by bookauthor.book");
    $bookauthors = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        $bookauthors[] = $cRecord;
    }
    for ($n=0; $n < count($books); $n++) {
        foreach($bookauthors as $key=>$value) {
            if ($books[$n]['book_id'] == $value['book']) $books[$n]['assemble'] .= $value['author'].', ';
        }
        $books[$n]['assemble'] = substr($books[$n]['assemble'], 0, strlen($books[$n]['assemble']) - 2);
    }    

    // список авторів 
    echo "<div id='authors-books'>";

    echo "<ul id='author-list'>"; // кнопка додати автора
    if ($login) {
        echo "<button id='author-add' type='button'> Додати автора </button><br>"; 
    };

    // кнопка повернення до списку авторів
    if ($current_author) echo "<a id='folder-list' class='visible' href='.'><img src='../assets/img/books2.png'>Список авторів ...</a>";

    foreach($authors as $key=>$value) { // відображення списку авторів
        $photo = "<span class='author-photo'></span>";
        if ($value['photo'] != '') $photo = "<img class='author-photo' src='".AUTHOR_PHOTO_FOLDER."{$value['photo']}'>";
        
        $active_item = '';
        if ($value['author_id']==$current_author) $active_item = 'active-item';
        if (!$current_author) $active_class = " visible";
        
        $btns = ''; // кнопки редагування / видалення
        if ($login) $btns = "<img id='author-edit' data-id='{$value['author_id']}' class='edit-button' src='../assets/img/edit-button.png'>";
        if ($value['cnt'] < 1) $btns .= "<img id='author-del' data-id='{$value['author_id']}' class='edit-button' src='../assets/img/close.png'>";

        echo
        "<li class='author'>".
        "{$btns}".
            "<a href='?author={$value['author_id']}' id='books-by-author' class='{$active_item}{$active_class}'>".
                "<span class='author-name '>{$describe}{$value['authorname']}</span>".
                "{$photo}".
            "<span class='books-by-author'>".
                "<img class='books click' src='../assets/img/openbook.png' class='click' >".
                "<span class='bage click'>{$value['cnt']}</span>".
            "</span>".
            "</a>".
            "</li>";
            if ($value['author_id'] == $current_author  && $value['describe'])
            echo
            "<p id='describe-text{$value['author_id']}' class='describe-text'>{$value['describe']}
            <img id='close-author-describe' class='close' src='../assets/img/close2.png'>
            </p>";

    }
    echo "</ul>";

    // визначаємо зображення, пов'язані з авторами
    $author_pict = array();
    foreach($authors as $key=>$value) {
        $author_pict[$value['photo']] = $value['authorname'] ? true : false;
    }

    // завантажуємо каталог зображень авторів
    $pictures = scandir(AUTHOR_PHOTO_FOLDER);
    array_shift($pictures);
    array_shift($pictures);
    $picture_list = "<div id='picture-list'>";
    $path = AUTHOR_PHOTO_FOLDER;
    foreach($pictures as $value) {
        $del = '';
        if (!$author_pict[$value]) $del = "<img data-id='del' data-file='{$value}' class='del-picture' src='../assets/img/close.png'>";
        $picture_list .= "<div><img src='{$path}{$value}'>${del}</div>";
    }
    $picture_list .= "</div>";
    

    // список книг
    echo "<div class='book-right'>";

    if (isset($books)) {

        $photo_folder = BOOK_PHOTO_FOLDER;
        foreach($books as $key=>$value){
        if ($value['authorID'] == $current_author) 
    {
                $root = ROOTFOLDER;
                $price = $value['price'] ? "Ціна: {$value['price']} грн" : '';
                $available = $value['available'] ? "Наявність: Так" : 'Наявність: Ні';
                $writer = $value['assemble'] ? "<img class='writer' src='../assets/img/pen.png'> {$value['assemble']}" : '';

                $book_picture = $value['picture'] != '' ? "<img src='{$photo_folder}{$value['picture']}'>" : '';
                echo "<div class='book-item' data-book='{$value['book_id']}'>
                <div>
                <span class='book-name'>&laquo;{$value['book']}&raquo; </span>
                <span class='book-author'> {$writer} </span>
                <span class='book-folder'>
                    <a href='{$root}/books/?folder={$value['folder_id']}&book={$value['book_id']}' title='Перейти до розділу {$value['folder']}'>
                        <img class='writer' src='../assets/img/books2.png'>{$value['folder']}
                    </a>
                </span>
                <span class='book-describe'>{$value['describe']}</span>
                </div>
                <span class='img'> {$book_picture} </span>
                <span class='book-describe'>{$price} </span>
                <span class='book-describe'>{$available}</span>
                </div>";
            }
            }
        }

    echo "</div>";
    echo "</div>"; //<div id='authors-books'>

?>
<script src='../assets/js/explorer.js'></script>
<script>
    document.addEventListener("DOMContentLoaded", ()=>{

    let currentBook;

    document.querySelector('#author-list').addEventListener('click', (event)=>{ // вішаємо обробник кліку по автору
        if (event.target.id == 'close-author-describe') event.target.parentElement.style.display = 'none';
    });

    document.querySelector('.book-right').addEventListener('click',(event)=>{// вішаємо обробник кліку по книзі
        let el = event.target;
        while (el != null && !el.matches('.book-item')) el = el.parentElement;
        if (el == null) return;

        el.classList.toggle('book-view');
        fade(el, 300);
        if (currentBook) if (currentBook != el) currentBook.classList.remove('book-view');
        el.scrollIntoView();
        window.scrollBy(0, -200);
        currentBook = el;
    })
// плавне відображення списку авторів
let authorItemList = document.querySelectorAll('li.author');

let itemTimeout = 10;
authorItemList.forEach((i)=>{
    setTimeout(()=>{
        fade(i, 30);
    }, itemTimeout);
    itemTimeout += 1;
});

// плавне відображення списку книг
let bookItemList = document.querySelectorAll('.book-item');

itemTimeout = 10;
bookItemList.forEach((i)=>{
    setTimeout(()=>{
        fade(i, 100);
    }, itemTimeout);
    itemTimeout += 10;
})

function addEdit(item) {
    function addEditForm(header, number = null) { // форма редагування - додавання
			modalWindow(header, `
			<form name='addEdit'  class="admin">
			<ul>
				<li><input type='text' placeholder='Автор' name='author'></li>
                <li><textarea name='describe' rows='5' placeholder='Про автора...'></textarea></li>
                <li>
                <img id="img-book" class="book-img" src="">

                    Зображення: <input type='text' disabled='' name='picture' style='width:15%'>
                    <button type='button' id='picture-clear'>Очистити зображення</button>
                    <button type='button' id='picture-choice'>Обрати зображення</button>
                    <br>
                    <?=$picture_list?>
                    <label class='button'>
                        Завантажити зображення
                        <input type='file' id='picture-upload' name="file" accept='image/*'></input>
                    </label>
                    <span class='wait'>Uploading... <img src='../assets/img/book.gif'></span>
                </li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addEdit;
				if (btn == 0) { // збереження форми в базі
					if (!item) { // додаваня запису
					queryInsert('authors', [
						['author', formAdmin.author.value],
						['describe', formAdmin.describe.value],
						['photo', formAdmin.picture.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							popUpWindow('Запис додано', ()=>{document.location.reload(true)});
						};
					}, '<?=PHP_PATH?>');
				} // додаваня запису
					if (item != null) { // редагування запису
					queryUpdate('authors', `authors.author_id=${item}`, [
						['author', formAdmin.author.value],
						['describe', formAdmin.describe.value],
						['photo', formAdmin.picture.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							popUpWindow('Запис змінено', ()=>{document.location.reload(true)});
						};
					}, '<?=PHP_PATH?>');
				} // редагування запису

				} // збереження форми в базі
			},
			'80%', 400); // modalwindow

        } // addEditForm

        if (item == null) { // додавання
			addEditForm ('Додати автора');
		}
		else { // редагування
            addEditForm('Редагувати автора', item);
            let formAdmin = document.forms.addEdit;
			queryGet(`select * from authors where author_id=${item}`, (response)=>{ // отримуємо елемент з бази
                // наповнюємо поля форми
				formAdmin.author.value = response[0].author;
				formAdmin.describe.value = response[0].describe;
				formAdmin.picture.value = response[0].photo;
                let imgBook = document.querySelector('form.admin #img-book');
                if (response[0].photo) {imgBook.setAttribute('src', '<?=AUTHOR_PHOTO_FOLDER?>' + response[0].photo)}
                else imgBook.style.display = 'none';

			}, '<?=PHP_PATH?>')
		}

// ---------------------------------------------------------------------------------------------------------------------
let formAdmin = document.forms.addEdit;

document.querySelector('#picture-choice').addEventListener('click', (event)=>{ // вибір зображення
    document.querySelector('#picture-list').style.display = 'flex';
})

document.querySelector('#picture-list').addEventListener('mouseleave', (event)=>{ // закриття вікна вибору зображень
    event.target.style.display = 'none';
})

document.querySelector('#picture-list').addEventListener('click', (event)=>{ // видалення зображення (лише тих, що не прив'язані до жодної книги)
    if (event.target.tagName == 'IMG') {
        if (event.target.dataset.id == 'del') {
            if (confirm(`Видалити ${event.target.dataset.file} ?`)) {
                queryDelFile(`<?=AUTHOR_PHOTO_FOLDER?>${event.target.dataset.file}`, (response)=>{
                    if (response.error == 0) {popUpWindow ('Файл видалено.')} else popUpWindow('Помилка! Файл не видалено.');
                }, '<?=PHP_PATH?>')
            }
            return;
        }
        let l = '<?=AUTHOR_PHOTO_FOLDER?>'.length;
        let s = event.target.getAttribute('src');
        formAdmin.picture.value = s.substr(l, s.length);
        event.currentTarget.style.display = 'none';
        }
})

document.querySelector('#picture-upload').addEventListener('change', (event)=>{ // завантаження зображення
    document.querySelector('.wait').style.visibility = 'visible';
    setTimeout(()=>{
    document.querySelector('.wait').style.visibility = 'hidden';
    upLoad(event.target.files[0], 'assets/img/authors/', (response)=>{
        if (response.error == 0 && response.upload) {
            formAdmin.picture.value = response.filename;
            popUpWindow(`Файл ${response.filename} завантажено.`);
        }
        if (response.error == 1) popUpWindow("Перевищено розмір файлу 200Mb.")
        if (response.error == 2) popUpWindow("Невірний формат файлу.")
        console.log(response.upload)
    }, '<?=PHP_PATH?>', 'image', 209715200)
}, 1000);
})

document.querySelector('#picture-clear').addEventListener('click', (event)=>{ // очистка зображення
    formAdmin.picture.value = '';
})

// -------------------------------

} // addEdit

	document.body.addEventListener('click', (event)=>{ // обробка кліку edit, del та add
		if (event.target.id == 'author-add') {
			addEdit(null);
		}
		if (event.target.id == 'author-edit') {
			addEdit(event.target.dataset.id);
			}
		if (event.target.id == 'author-del') { // видалення
			modalWindow('Видалення елементу', 'Ви підтверджуєте видалення цього елементу?', ['Залишити', '-Видалити'], (n)=>{
				if (n == 1) {
					queryDelete('authors', `author_id=${event.target.dataset.id}`, (response)=>{
						if (!response.sql) {console.log(response)} else {
							popUpWindow ('Запис видалено.', ()=>{document.location.reload(true)});
						}
                    }, '<?=PHP_PATH?>');
				}
			}, '60%');
		};
	})

}) //onload
</script>
