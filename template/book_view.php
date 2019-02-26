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
    $query = mysql_query("SELECT 
        folders.folder_id, folders.folder, count(*) as cnt
        FROM folders
        left join books on books.folder = folders.folder_id
        GROUP BY folders.folder_id
        ORDER BY cnt DESC");
    $folders = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        if ($cRecord['folder_id'] == $current_folder) array_unshift($folders, $cRecord); else {
        $folders[] = $cRecord;
        }
    }

    // автори книг
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

    $login = getLogin();
?>
<div class="books">
    
    <div class="book-left">
    <?
        if ($current_folder) echo "<a id='folder-list' class='visible' href='.'><img src='../assets/img/books2.png'>Список розділів ...</a>";
    ?>
        <ul>
            <? // відображення списку розділів
                if ($login) {
                    if ($current_folder) echo "<button type='button'> Додати книгу </button><br>"; else 
                    echo "<button type='button'> Додати розділ </button><br>";
                };

                foreach($folders as $key=>$value) { 
                    $active_class = $value['folder_id'] == $current_folder ? "class='active'" : '';
                    if (!$current_folder) $active_class = "class = 'visible'";
                    $btns = '';
                    if ($login) $btns = "<img class='edit-button' src='../assets/img/edit-button.png'>";
                    if ($value['cnt'] < 1) $btns .= "<img class='edit-button' id='edit' src='../assets/img/close.png'";
                    echo "
                    <a {$active_class} href='?folder={$value['folder_id']}'>
                    <li >
                    {$btns}
                    <img id='open-book' src='../assets/img/openbook.png'>
                    <img id='close-book' src='../assets/img/book.png'>
                    <span>
                    {$value['folder']}
                    </span>
                    <span class='bage'>{$value['cnt']}</span>
                    </li></a>";
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
                    $price = $value['price'] ? "Ціна: {$value['price']} грн" : '';
                    $available = $value['available'] ? "Наявність: Так" : 'Наявність: Ні';
                    $writer = $value['assemble'] ? "<img class='writer' src='../assets/img/pero.png'> {$value['assemble']}" : '';

                    $book_picture = $value['picture'] != '' ? "<img src='{$photo_folder}{$value['picture']}'>" : '';
                    echo "<div class='book-item' data-mainelement='1' data-book='{$value['book_id']}'>
                    <div>
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
<script src='../assets/js/explorer.js'></script>
<script>
    document.addEventListener("DOMContentLoaded", ()=>{
    let margin = 0;
    let el = document.querySelectorAll('.book-left li');
    let interval = setInterval(()=>{
        if (margin > 20) clearInterval(interval);
        margin++;
        el.forEach((i)=>{
            i.style.margin = `${margin}px 0`;
        })
    }, 10);

    let currentBook;
    document.querySelector('.book-right').addEventListener('click',(event)=>{
        let el = event.target;
        while (!el.dataset.mainelement) el = el.parentElement;
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

}) // onload
</script>