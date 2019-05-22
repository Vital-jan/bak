<!DOCTYPE html>
<?
    $current_author = $_GET['author'];
    $photo_folder = BOOK_PHOTO_FOLDER;
    $login = getLogin();

    // завантажуємо з бд авторів
    $where = $current_author ? "where author_id={$current_author}":'';
    
    // $query = mysql_query(
    //     "SELECT authors.author, authors.author_id, cnt, authors.photo, authors.describe FROM authors LEFT JOIN (SELECT distinct bookauthor.author, Count(*) as cnt FROM bookauthor group by bookauthor.author ) AS authorcount ON authors.author_id = authorcount.author
    // {$where} 
    // ORDER BY cnt DESC"
    // );
    $selectauthors = "select *, count(*) as cnt from 
    (select distinct bookauthor.book, bookauthor.author, authors.author_id, authors.author as authorname, `authors`.describe, `authors`.photo from bookauthor left join authors on bookauthor.author=author_id)
    as s1 
    {$where}
    group by author 
    order by cnt desc";
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

let itemTimeout = 30;
authorItemList.forEach((i)=>{
    setTimeout(()=>{
        fade(i, 200);
    }, itemTimeout);
    itemTimeout += 30;
});

// плавне відображення списку книг
let bookItemList = document.querySelectorAll('.book-item');

itemTimeout = 50;
bookItemList.forEach((i)=>{
    setTimeout(()=>{
        fade(i, 200);
    }, itemTimeout);
    itemTimeout += 50;
})

function addEdit(item) {
    function addEditForm(header, number = null) { // форма редагування - додавання
			modalWindow(header, `
			<form name='addEdit'  class="admin">
			<ul>
				<li><input type='text' placeholder='Автор' name='author'></li>
                <li><textarea name='describe' rows='5' placeholder='Про автора...'></textarea></li>
                <li><button type='button'>Обрати зображення</button></li>
                <li><button type='button'>Завантажити зображення</button></li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addEdit;
				if (btn == 0) { // збереження форми в базі
					if (!item) { // додаваня запису
					queryInsert('authors', [
						['author', formAdmin.author.value],
						['describe', formAdmin.describe.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис додано.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // додаваня запису
					if (item != null) { // редагування запису
					queryUpdate('authors', `authors.author_id=${item}`, [
						['author', formAdmin.author.value],
						['describe', formAdmin.describe.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис змінено.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // редагування запису

				} // збереження форми в базі
			},
			'80%', 300); // modalwindow

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
			}, '<?=PHP_PATH?>')
		}

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
							alert ('Запис видалено.');
							document.location.reload(true);
						}
                    }, '<?=PHP_PATH?>');
				}
			}, '60%');
		};
	})

}) //onload
</script>
