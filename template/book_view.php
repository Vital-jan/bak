<?
    $current_folder = $_GET['folder'];
    
    // книги
    if ($current_folder) {
    $query = mysql_query("SELECT * FROM books WHERE deleted = 0 ORDER BY book");
    $books = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        $books[] = $cRecord;
        }
    }

    // розділи книг
    $where = $current_folder ? "where folders.folder_id={$current_folder}" : '';

    $query = mysql_query(
    "SELECT 
    folders.folder_id, folders.folder, cnt from folders left join 
    (select books.folder, count(*) as cnt from books group by books.folder) as sel
    on sel.folder = folders.folder_id
    {$where}
    ORDER BY cnt DESC
    ");

    $folders = array(); 
    while ($cRecord = mysql_fetch_assoc($query)) {
        $flist .= "<option id='folder-select' value='{$cRecord['folder_id']}'>{$cRecord['folder']}</option>";
        if ($cRecord['folder_id'] == $current_folder) array_unshift($folders, $cRecord); else {
            $folders[] = $cRecord;
        }
    }
    
    $query = mysql_query("SELECT * from folders ORDER BY folders.folder ASC");// для поля select редагування книг
    $flist = '';
    while ($cRecord = mysql_fetch_assoc($query)) {
        $flist .= "<option id='folder-select' value='{$cRecord['folder_id']}'>{$cRecord['folder']}</option>";
    }
    
    // перелік авторів для кожної книги
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

    // перелік авторів для поля додавання авторів
    $query = mysql_query("SELECT authors.author, authors.author_id  FROM authors order by author ASC");
    $authors = '';
    while ($cRecord = mysql_fetch_assoc($query)) {
        $authors .= "<li data-id='{$cRecord['author_id']}'>{$cRecord['author']}</li>";
    }

    $login = getLogin();

?>
<div class="books">
    <div class="book-left">
    <?
        if ($current_folder) echo "<a id='folder-list' class='visible' href='.'><img src='../assets/img/books2.png'>Список розділів ...</a>";
    ?>
        <ul id='folders'>
            <? // відображення списку розділів
                if ($login) {
                    if ($current_folder) echo "<button id='book-add' type='button'> Додати книгу </button><br>"; else 
                    echo "<button id='folder-add' type='button'> Додати розділ </button><br>";
                };

                foreach($folders as $key=>$value) { 
                    $active_class = $value['folder_id'] == $current_folder ? "class='active'" : '';
                    if (!$current_folder) $active_class = "class = 'visible'";

                    $btns = ''; // кнопки edit та del
                    if ($login) $btns = "<img id='folder-edit' data-id='{$value['folder_id']}' class='edit-button' src='../assets/img/edit-button.png'>";
                    if ($value['cnt'] < 1) $btns .= "<img id='folder-del' data-id='{$value['folder_id']}' class='edit-button' src='../assets/img/close.png'>";

                    echo "
                    <li>
                    {$btns}
                    <a {$active_class} href='?folder={$value['folder_id']}'>
                    <img id='open-book' src='../assets/img/openbook.png'>
                    <img id='close-book' src='../assets/img/book.png'>
                    <span>
                    {$value['folder']}
                    </span>
                    <span class='bage'>{$value['cnt']}</span>
                    </a>
                    </li>
                    ";
                }
            ?>
        </ul>
    </div>

    <? // відображення списку книг
            echo "<div class='book-right'>";

            if (isset($books)) {

            $photo_folder = BOOK_PHOTO_FOLDER;
            foreach($books as $key=>$value){
                if ($value['folder'] == $current_folder) 
                {
                    $btns = ''; // кнопки edit та del
                    if ($login) $btns = "<img id='book-edit' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/edit-button.png'>";
                    $btns .= "<img id='book-del' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/close.png'>";

                    $price = $value['price'] ? "Ціна: {$value['price']} грн" : '';
                    $available = $value['available'] ? "Наявність: Так" : 'Наявність: Ні';
                    $writer = $value['assemble'] ? "<img class='writer' src='../assets/img/pero.png'> {$value['assemble']}" : '';

                    $book_picture = $value['picture'] != '' ? "<img src='{$photo_folder}{$value['picture']}'>" : '';
                    echo "<div class='book-item' data-book='{$value['book_id']}'>
                    <div>
                    <span class='btns'> {$btns} </span>
                        <span class='book-name'>&laquo;{$value['book']}&raquo; </span>
                        <span class='book-author'> {$writer} </span>
                        <span class='book-describe'>{$value['describe']}</span>
                    </div>
                    <span class='img'> {$book_picture} </span>
                    <span class='book-describe'>{$price} </span>
                    <span class='book-describe'>{$available}</span>
                    </div>";
                }
                }
            }
            ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", ()=>{

    let margin = 0;
    let el = document.querySelectorAll('.book-left li');

    let interval = setInterval(()=>{ // плавне відображення розділів
        if (margin > 20) clearInterval(interval);
        margin++;
        el.forEach((i)=>{
            i.style.margin = `${margin}px 0`;
        })
    }, 10);

    let currentBook; // обрана книга

    function addBook() {
        modalWindow('Створити книгу', `
			<form name='addBook'  class="admin">
			<ul>
                <h3>Книга буде додана в поточний розділ. Пізніше розділ книги можна змінити.</h3>
				<li>Назва книги:<input type='text' placeholder='Назва книги' name='book'></li>
			</ul>
			`
			, ['+Створити', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addBook;
				if (btn == 0) { // збереження форми в базі (додаваня запису)
					queryInsert('books', [
						['book', formAdmin.book.value],
						['#folder', <?=$current_folder?>]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис додано.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>'); 
				} // збереження форми в базі
			},
			'80%', 300); // modalwindow
    }

        function bookauthorCreate(item){ // створити перелік авторів поточної книги
            queryGet('SELECT bookauthor.bookauthor_id, bookauthor.book, authors.author FROM bookauthor LEFT JOIN authors on bookauthor.author = authors.author_id', bookauthorResolve, '<?=PHP_PATH?>');
            function bookauthorResolve(resolve) {
            let s = '';
            resolve.forEach((i)=>{
                if (i.book == item) s += `<li><img src='../assets/img/close.png' title='Вилучити автора' data-id='${i.bookauthor_id}'>${i.author}</li>`;
            })
            document.querySelector('form #bookauthor').innerHTML = s;
            }
        };

    function editBook(item) { // редагування книги

        function selectRefresh (id, value){ // оновлення ел-тів select.
            let itemList = document.querySelectorAll(`select option#${id}`);
            itemList.forEach((i, n)=> {
                i.removeAttribute('selected');
                if (i.value == value) i.setAttribute('selected',''); 
                if (i.value == '' && value == null) i.setAttribute('selected','');
            });
        }
			modalWindow('Редагувати книгу', `
			<form name='editBook'  class="admin">
			<ul>
				<li>Книга:<input type='text' placeholder='Книга' name='book'></li>
                <li>Розділ:
                    <select name='folder'>
                    <?=$flist?>
                    </select>
                </li>

				<li>Опис:</li>
				<li><textarea placeholder='Опис книги' name='describe' rows=5></textarea></li>
                <li>Автори:</li>
                <ul id="bookauthor"></ul>
                <li><button id='author-add' type="button">Додати автора</button></li>
                <ul id="bookauthor-select">
                <?=$authors?>
                </ul>
				<li>Ціна:<input type='text' placeholder='Ціна' name='price'></li>
				<li>Наявність:
                    <select name="available">
                        <option value="" id="available"></option>
                        <option value="0" id="available">Ні</option>
                        <option value="1" id="available">Так</option>
                    </select>
                </li>
				<li>Зображення:<input type='text' placeholder='Оберіть файл...' name='picture' disabled></li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.editBook;
				if (btn == 0) { // збереження форми в базі
					if (!item) {
                        alert('Помилка! Не обрано книгу.');
                        return;
				    } 
					if (item != null) { // редагування запису
					queryUpdate('books', `books.book_id=${item}`, [
						['book', formAdmin.book.value],
						['describe', formAdmin.describe.value],
						['#price', formAdmin.price.value],
						['picture', formAdmin.picture.value],
						['available', formAdmin.available.value],
						['folder', formAdmin.folder.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис змінено.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // редагування запису

				} // збереження форми в базі
			},
			'80%', 500); // modalwindow

document.querySelector('#bookauthor').addEventListener('click', (event)=>{
    if (!event.target.dataset.id) return;
    console.log('delete ', event.target.dataset.id)
    queryDelete('bookauthor', `bookauthor.bookauthor_id=${event.target.dataset.id}`, (response)=>{
                    bookauthorCreate(item)
                },
                '<?=PHP_PATH?>');

})

document.querySelector('#author-add').addEventListener('click', (event)=>{
    document.querySelector('#bookauthor-select').style.display = 'block';
})

document.querySelector('#bookauthor-select').addEventListener('mouseleave', (event)=>{
    event.target.style.display = 'none';
})

document.querySelector('#bookauthor-select').addEventListener('click', (event)=>{
    queryInsert('bookauthor', [
                        ['#book', item],
                        ['#author', `${event.target.dataset.id}`]
                    ], ()=>{
                        bookauthorCreate(item);
                    }, '<?=PHP_PATH?>');

})

            let formAdmin = document.forms.editBook;
			queryGet(`select * from books where book_id=${item}`, (response)=>{ // отримуємо елемент з бази
                // наповнюємо поля форми
				formAdmin.book.value = response[0].book;
				formAdmin.describe.value = response[0].describe;
				formAdmin.price.value = response[0].price;
				formAdmin.picture.value = response[0].picture;
                selectRefresh('available', response[0].available);
                selectRefresh('folder-select', response[0].folder);
                bookauthorCreate(item);
			}, '<?=PHP_PATH?>')

    }

    document.querySelector('.book-right').addEventListener('click',(event)=>{ // обробник кліку по книзі
        let el = event.target;
        if (el.id == 'book-edit') { // кнопка edit
            editBook(el.dataset.id);
            return;
        }
        if (el.id == 'book-del') { // кнопка del
            modalWindow('Видалення книги', 'Ви підтверджуєте видалення цієї книги?', ['Залишити', '-Видалити'], (n)=>{
			if (n == 1) {
				queryDelete('books', `book_id=${el.dataset.id}`, (response)=>{
					if (!response.sql) {console.log(response)} else {
						alert ('Запис видалено.');
						document.location.reload(true);
					}
                }, '<?=PHP_PATH?>');
			}
		}, '60%');
        return;
        }

        while (el != null && !el.matches('.book-item')) el = el.parentElement;
        if (el == null) return;

        el.classList.toggle('book-view');
        if (currentBook) if (currentBook != el) currentBook.classList.remove('book-view');
        fade(el, 300);
        el.scrollIntoView();
        window.scrollBy(0, -200)
        currentBook = el;
    })

// плавне відображення списку книг
let bookItemList = document.querySelectorAll('.book-item');

let itemTimeout = 30;
bookItemList.forEach((i)=>{
    setTimeout(()=>{
        fade(i, 300);
    }, itemTimeout);
    itemTimeout += 30;
})

function addEditFolder(item) { // редагування-додавання розділу
    function addEditForm(header, number = null) { // форма редагування - додавання
			modalWindow(header, `
			<form name='addEditFolder'  class="admin">
			<ul>
				<li><input type='text' placeholder='Розділ' name='folder'></li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addEditFolder;
				if (btn == 0) { // збереження форми в базі
					if (!item) { // додаваня запису
					queryInsert('folders', [
						['folder', formAdmin.folder.value],
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис додано.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // додаваня запису
					if (item != null) { // редагування запису
					queryUpdate('folders', `folders.folder_id=${item}`, [
						['folder', formAdmin.folder.value],
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
			addEditForm ('Додати розділ');
		}
		else { // редагування
            addEditForm('Редагувати розділ', item);
            let formAdmin = document.forms.addEditFolder;
			queryGet(`select * from folders where folder_id=${item}`, (response)=>{ // отримуємо елемент з бази
                // наповнюємо поля форми
				formAdmin.folder.value = response[0].folder;
			}, '<?=PHP_PATH?>')
		}
} // addEditFolder

document.querySelector('.book-left').addEventListener('click', (event)=>{ // обробка кліку edit, del та add для розділів та add для книг

	if (event.target.id == 'book-add') { // додати книгу
		addBook();
    }

	if (event.target.id == 'folder-add') { // додати розділ
		addEditFolder(null);
    }
    
	if (event.target.id == 'folder-edit') { // редагувати розділ
		addEditFolder(event.target.dataset.id);
        }
        
	if (event.target.id == 'folder-del') { // видалення розділу

		modalWindow('Видалення розділу', 'Ви підтверджуєте видалення цього розділу?', ['Залишити', '-Видалити'], (n)=>{
			if (n == 1) {
				queryDelete('folders', `folder_id=${event.target.dataset.id}`, (response)=>{
					if (!response.sql) {console.log(response)} else {
						alert ('Запис видалено.');
						document.location.reload(true);
					}
                }, '<?=PHP_PATH?>');
			}
		}, '60%');
	};
})


}) // onload
</script>