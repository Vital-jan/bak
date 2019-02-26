<!DOCTYPE html>
<?
    $current_author = $_GET['author'];
    $photo_folder = BOOK_PHOTO_FOLDER;
    $login = getLogin();

    // завантажуємо з бд авторів
    $query = mysql_query("SELECT authors.author, authors.author_id, Count(*) AS cnt, bookauthor.book, books.book, authors.photo, authors.describe FROM
    authors INNER JOIN bookauthor ON authors.author_id = bookauthor.author INNER JOIN books ON bookauthor.book = books.book_id
    group by authors.author_id ORDER BY cnt DESC, authors.author ASC");
    
    $authors = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        if ($cRecord['author_id'] == $current_author) array_unshift($authors, $cRecord); else { // обраного автора на перше місце
        $authors[] = $cRecord;
        }
    }

    // завантажуємо книги авторів
    $query = mysql_query("SELECT bookauthor.book, books.book, books.book_id, bookauthor.author as authorID, books.describe, books.price, books.available, books.picture
    FROM bookauthor
    LEFT JOIN books ON bookauthor.book = books.book_id
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

    echo "<ul id='author-list'>";
    if ($login) {
        echo "<button type='button'> Додати автора </button><br>"; 
    };
    if ($current_author) echo "<a id='folder-list' class='visible' href='.'><img src='../assets/img/books2.png'>Список авторів ...</a>";
    foreach($authors as $key=>$value) {
        $photo = "<span class='author-photo'></span>";
        if ($value['photo'] != '') $photo = "<img class='author-photo' src='".AUTHOR_PHOTO_FOLDER."{$value['photo']}'>";

        $btns = '';
        if ($login) $btns = "<img class='edit-button' src='../assets/img/edit-button.png'>";
        if ($value['cnt'] < 1) $btns .= "<img class='edit-button' src='../assets/img/close.png'";

        $active_item = '';
        if ($value['author_id']==$current_author) $active_item = 'active-item';
        if (!$current_author) $active_class = " visible";


        echo
            "<a href='?author={$value['author_id']}' id='books-by-author' class='{$active_item}{$active_class}'>".
            "<li class='author'>".
            "{$btns}".
                "<span class='author-name '>{$describe}{$value['author']}</span>".
                "{$photo}".
            "<span class='books-by-author'>".
                "<img class='books click' src='../assets/img/openbook.png' class='click' >".
                "<span class='bage click'>{$value['cnt']}</span>".
            "</span>".
            "</li>".
            "</a>";
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
                $price = $value['price'] ? "Ціна: {$value['price']} грн" : '';
                $available = $value['available'] ? "Наявність: Так" : 'Наявність: Ні';
                $writer = $value['assemble'] ? "<img class='writer' src='../assets/img/pen.png'> {$value['assemble']}" : '';

                $book_picture = $value['picture'] != '' ? "<img src='{$photo_folder}{$value['picture']}'>" : '';
                echo "<div class='book-item' data-book='{$value['book_id']}'>
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
        while (!el.matches('.book-item')) el = el.parentElement;
        el.classList.toggle('book-view');
        fade(el, 300);
        if (currentBook) if (currentBook != el) currentBook.classList.remove('book-view');
        el.scrollIntoView();
        window.scrollBy(0, -200)
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


}) //onload
</script>
