<?
    $current_folder = $_GET['folder'];
    $book_item = $_GET['book'];
    
    if ($current_folder) { // якщо обраний поточний розділ:
        // завантажуємо перелік книг
        $query = mysqli_query($GLOBALS['db_connect'], "SELECT * FROM books WHERE books.folder=".$current_folder." ORDER BY created DESC");
        $books = array();
        while ($cRecord = mysqli_fetch_assoc($query)) {
            $books[] = $cRecord;
        }
        
        // визначаємо зображення, пов'язані з книжками
        $bookpict = array();
        foreach($books as $key=>$value) {
            $bookpict[$value['picture']] = $value['book'] ? true : false;
        }
        
        // завантажуємо каталог зображень книг (html)
        $pictures = scandir(BOOK_PHOTO_FOLDER);
        array_shift($pictures);
        array_shift($pictures);
        $picture_list = "<div id='picture-list'>";
        $path = BOOK_PHOTO_FOLDER;
        foreach($pictures as $value) {
            $del = '';
            if (!$bookpict[$value]) $del = "<img data-id='del' data-file='{$value}' class='del-picture' src='../assets/img/close.png'>";
            $picture_list .= "<div><img src='{$path}{$value}'>${del}</div>";
        }
        $picture_list .= "</div>";
        
        // створюємо перелік авторів через кому для кожної книги в масиві $books
        require '../common/authors_assemble.php';
        
        // перелік авторів для поля додавання авторів
        $query = mysqli_query($GLOBALS['db_connect'], "select author_id, `authors`.author, cnt from authors left join (select *, count(*) as cnt from bookauthor group by author) as sel2 on author_id = sel2.author");
        $authors = '';
        while ($cRecord = mysqli_fetch_assoc($query)) {
            $del_btn = $cRecord['cnt'] == null ? "<img src='../assets/img/close.png' data-delauthor={$cRecord['author_id']} data-delauthortext={$cRecord['author']}>" : "";
            $authors .= "<li data-id='{$cRecord['author_id']}'>{$del_btn}{$cRecord['author']}</li>";
        }
    }
    
    

    // завантажуємо розділи книг
    $where = $current_folder ? "where folders.folder_id={$current_folder}" : '';

    $query = mysqli_query($GLOBALS['db_connect'], 
    "SELECT 
    folders.folder_id, folders.folder, cnt from folders left join 
    (select books.folder, count(*) as cnt from books group by books.folder) as sel
    on sel.folder = folders.folder_id
    {$where}
    ORDER BY cnt DESC
    ");

    $folders = array(); 
    while ($cRecord = mysqli_fetch_assoc($query)) {
        $flist .= "<option id='folder-select' value='{$cRecord['folder_id']}'>{$cRecord['folder']}</option>";
        if ($cRecord['folder_id'] == $current_folder) array_unshift($folders, $cRecord); else {
            $folders[] = $cRecord;
        }
    }
    
    $query = mysqli_query($GLOBALS['db_connect'], "SELECT * from folders ORDER BY folders.folder ASC");// для поля select редагування книг
    $flist = '';
    while ($cRecord = mysqli_fetch_assoc($query)) {
        $flist .= "<option id='folder-select' value='{$cRecord['folder_id']}'>{$cRecord['folder']}</option>";
    }
    

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

    <?
    // відображення списку книг
        echo "<div class='book-right'>";
        if ($current_folder) // якщо обраний поточний розділ:
            require '../common/books_list_echo.php';
        echo "</div>";
    ?>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", ()=>{

    function scrollToBook (item) { // прокрутка до книги в разі переходу з іншої сторінки
        let elem = document.querySelector(`[data-book='${item}']`);
        if (elem) elem.scrollIntoView(true);
        window.scrollBy(0, -250)
    }

    let currentBook = <? if (isset ($book_item)) echo $book_item; else echo 'null'?>; // // обрана книга в разі переходу зі сторінки "Автори"
    let currentBookEl;
    if (currentBook) {
        currentBookEl = document.querySelector(`[data-book='${currentBook}']`);
        currentBookEl.classList.toggle('book-view');
    }

    let currentFolder = '<?if (isset($_GET['folder'])) echo $_GET['folder']?>';

    let margin = 0;
    let el = document.querySelectorAll('.book-left li');

    let interval = setInterval(()=>{ // плавне відображення розділів
        if (margin > 20) clearInterval(interval);
        margin++;
        el.forEach((i)=>{
            i.style.margin = `${margin}px 0`;
        })
    }, 10);


    function addBook() { // Додати книгу ------------------------------------------------
        modalWindow('Додати нову книгу', `
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
							popUpWindow ('Запис додано.', ()=>{
                                let newBook = 0;
                                queryGet(`select book_id from books where book=${formAdmin.book.value}`, (books)=>{
                                    let newBook = books[0].book_id;
                                    document.location.href=`../books/?folder=${currentFolder}&book=${newBook}`;
                                }, '<?=PHP_PATH?>');
                                });
						};
					}, '<?=PHP_PATH?>'); 
				} // збереження форми в базі
			},
			'80%', 300); // modalwindow
    }

        function bookauthorCreate(item){ // створити перелік авторів поточної книги
            queryGet('SELECT bookauthor.bookauthor_id, bookauthor.book, authors.author FROM bookauthor LEFT JOIN authors on bookauthor.author = authors.author_id', bookauthorResolve, '<?=PHP_PATH?>');
            function bookauthorResolve(resolve) {
            let s = "<li>Автори: <img src='../assets/img/add.png' id='author-add'></li>";
            
            resolve.forEach((i)=>{
                if (i.book == item) s += `<li><img src='../assets/img/close.png' title='Вилучити автора' data-id='${i.bookauthor_id}'>${i.author}</li>`;
            })
            document.querySelector('form #bookauthor').innerHTML = s;
            document.querySelector('#author-add').addEventListener('click', (event)=>{
                document.querySelector('#bookauthor-select').style.display = 'block';
            })

            }
        };

    function editBook(item) { // редагування книги ----------------------------------------------------

        function selectRefresh (id, value){ // оновлення ел-тів select.
            let itemList = document.querySelectorAll(`select option#${id}`);
            itemList.forEach((i, n)=> {
                i.removeAttribute('selected');
                if (i.value == value) i.setAttribute('selected',''); 
                if (i.value == '' && value == null) i.setAttribute('selected','');
            });
        }//----------
			modalWindow('Редагувати книгу', `
			<form name='editBook'  class="admin">
			<ul>
				<li>Книга:<input type='text' placeholder='Книга' name='book'></li>
                <li>Розділ:
                    <select name='folder'>
                    <?=$flist?>
                    </select>
                </li>

                <li><textarea placeholder='Опис книги' name='describe' rows=5></textarea></li>
                
                <ul id="bookauthor-select">
                    <?=$authors?>
                </ul>
                
                <?=$picture_list?> 

                <li>
                    <ul id="bookauthor"></ul>
                    <img id="img-book" class="book-img" src="">
                    Сторінок:<input style='width:30%' type='text' placeholder='Кількість сторінок' name='pages'>
                    <br>
                    Дата:<input style='width:30%' type='date' placeholder='Дата виходу' name='date'>
                    <br>
                    Ціна:<input style='width:30%' type='text' placeholder='Ціна' name='price'>
                    <br>
                    Наявність:
                        <select name="available">
                            <option value="" id="available"></option>
                            <option value="0" id="available">Ні</option>
                            <option value="1" id="available">Так</option>
                        </select>
                </li>
                <li>
                </li>
                <hr>
                <li>Зображення:<input style='width:20%' type='text' placeholder='Оберіть зображення...' name='picture' disabled>
                <button class='button' type='button' id='picture-clear'>Очистити</button>
                <button class='button' type='button' id='picture-choice'>Змінити зображення</button>

                <label class='button'>
                Завантажити зображення
                <input type='file' id='picture-upload' name="file" accept='image/*'></input>
                </label>
                <span class='wait'>Uploading... <img src='../assets/img/book.gif'></span>
                </li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.editBook;
				if (btn == 0) { // збереження форми в базі
					if (!item) {
                        popUpWindow('Помилка! Не обрано книгу.', ()=>{return});
				    } 
					if (item != null) { // редагування запису
					queryUpdate('books', `books.book_id=${item}`, [
						['book', formAdmin.book.value],
						['describe', formAdmin.describe.value],
						['pages', formAdmin.pages.value],
						['created', formAdmin.date.value],
						['#price', formAdmin.price.value],
						['picture', formAdmin.picture.value],
						['available', formAdmin.available.value],
						['folder', formAdmin.folder.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							popUpWindow ('Запис змінено.', ()=>{
                                document.location.href=`../books/?folder=${currentFolder}&book=${item}`;
                            });
						};
					}, '<?=PHP_PATH?>');
				} // редагування запису

				} // збереження форми в базі
			},
            '80%', '70%'); // modalwindow


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
                queryDelFile(`<?=BOOK_PHOTO_FOLDER?>${event.target.dataset.file}`, (response)=>{
                    if (response.error == 0) {popUpWindow ('Файл видалено.')} else popUpWindow('Помилка! Файл не видалено.',undefined,undefined,undefined,'red');
                }, '<?=PHP_PATH?>')
            }
            return;
        }
        let l = '<?=BOOK_PHOTO_FOLDER?>'.length;
        let s = event.target.getAttribute('src');
        formAdmin.picture.value = s.substr(l, s.length);
        event.currentTarget.style.display = 'none';
        }
})

document.querySelector('#picture-upload').addEventListener('change', (event)=>{ // завантаження зображення
    document.querySelector('.wait').style.visibility = 'visible';
    let stm = setTimeout(()=>{
    document.querySelector('.wait').style.visibility = 'hidden';
    upLoad(event.target.files[0], 'assets/img/books/', (response)=>{
        if (response.error == 0 && response.upload) {
            formAdmin.picture.value = response.filename;
            popUpWindow(`Файл ${response.filename} завантажено.`);
        }
        if (response.error == 1) popUpWindow("Перевищено розмір файлу 200Mb.")
        if (response.error == 2) popUpWindow("Невірний формат файлу.")
    }, '<?=PHP_PATH?>', 'image', 209715200)
}, 1000);
})

document.querySelector('#picture-clear').addEventListener('click', (event)=>{ // очистка зображення
    formAdmin.picture.value = '';
})


document.querySelector('#bookauthor').addEventListener('click', (event)=>{
    if (!event.target.dataset.id) return;
    queryDelete('bookauthor', `bookauthor.bookauthor_id=${event.target.dataset.id}`, (response)=>{
                    bookauthorCreate(item)
                },
                '<?=PHP_PATH?>');

})


document.querySelector('#bookauthor-select').addEventListener('mouseleave', (event)=>{ // закриття вікна долучення автора до книги
    event.target.style.display = 'none';
})

document.querySelector('#bookauthor-select').addEventListener('click', (event)=>{ // долучення автора до книги
    queryInsert('bookauthor', [
                        ['#book', item],
                        ['#author', `${event.target.dataset.id}`]
                    ], ()=>{
                        bookauthorCreate(item);
                    }, '<?=PHP_PATH?>');

})

document.querySelector('#bookauthor-select').addEventListener('click', (event)=>{ //видалення безкнижкового автора з бази
    if (event.target.dataset.delauthor) {
        modalWindow(`Видалити автора`,`Бажаєте видалити автора ${event.target.dataset.delauthortext} з бази даних?`, ['Залишити', '-Видалити'], (n)=>{
            if (n == 1) queryDelete('authors', `author_id=${event.target.dataset.delauthor}`, (response)=>{
                if (response.sql) popUpWindow('Видалено. Після поновлення сторінки автор видалиться з переліку.')
            }, '<?=PHP_PATH?>')
        }, undefined,undefined,undefined,undefined,undefined,undefined,undefined, 'modal-window2');
    }
})

            let formAdmin = document.forms.editBook;
			queryGet(`select * from books where book_id=${item}`, (response)=>{ // отримуємо елемент з бази
                // наповнюємо поля форми
				formAdmin.book.value = response[0].book;
				formAdmin.describe.value = response[0].describe;
				formAdmin.price.value = response[0].price;
				formAdmin.pages.value = response[0].pages;
				formAdmin.date.value = response[0].created;
                formAdmin.picture.value = response[0].picture;
                let imgBook = document.querySelector('form.admin #img-book');
                if (response[0].picture) {imgBook.setAttribute('src', '<?=BOOK_PHOTO_FOLDER?>' + response[0].picture)}
                else imgBook.style.display = 'none';
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
            modalWindow('Видалення книги', `Ви підтверджуєте видалення цієї книги ${el.parentElement.nextElementSibling.innerText}?`, ['Залишити', '-Видалити'], (n)=>{
			if (n == 1) {
				queryDelete('books', `book_id=${el.dataset.id}`, (response)=>{
					if (!response.sql) {console.log(response)} else {
						popUpWindow ('Запис видалено.', ()=>{document.location.href=`../books/?folder=${currentFolder}`});
					}
                }, '<?=PHP_PATH?>');
			}
		}, '60%');
        return;
        }

        if (el.id == 'copy-link') { // кнопка 'копіювати'
            localStorage.setItem('link',`<a href='<?=ROOTFOLDER?>books/?folder=<?=$current_folder?>&book=${el.dataset.id}'>${el.parentElement.nextElementSibling.innerText}</a>`);
            return;
        }

        while (el != null && !el.matches('.book-item')) el = el.parentElement;
        if (el == null) return;

        el.classList.toggle('book-view');
        if (currentBook) if (currentBookEl != el) currentBookEl.classList.remove('book-view');
        fade(el, 300);
        el.scrollIntoView();
        window.scrollBy(0, -250)
        currentBookEl = el;
    })

// плавне відображення списку книг
let bookItemList = document.querySelectorAll('.book-item');

let itemTimeout = 10;
bookItemList.forEach((i)=>{
    setTimeout(()=>{
        fade(i, 100);
    }, itemTimeout);
    itemTimeout += 5;
})

scrollToBook(currentBook);

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
							popUpWindow ('Запис додано.', ()=>{document.location.reload(true);});
							
						};
					}, '<?=PHP_PATH?>');
				} // додаваня запису
					if (item != null) { // редагування запису
					queryUpdate('folders', `folders.folder_id=${item}`, [
						['folder', formAdmin.folder.value],
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							popUpWindow ('Запис змінено.', ()=>{
                                document.location.href=`../books/?folder=${currentFolder}`});
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
						popUpWindow ('Запис видалено.', ()=>{document.location.reload(true)});
					}
                }, '<?=PHP_PATH?>');
			}
		}, '60%');
	};
})

}) // onload
</script>