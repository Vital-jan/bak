<!DOCTYPE html>
<?
    $current_author = $_GET['author'];
    $photo_folder = BOOK_PHOTO_FOLDER;

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
    $query = mysql_query("SELECT bookauthor.book, books.book, bookauthor.author as authorID, books.picture
    FROM bookauthor
    LEFT JOIN books ON bookauthor.book = books.book_id
    ");

    $books = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $books[] = $cRecord;
    }

    // список авторів 
    echo "<div id='authors-books'>
        <ul id='author-list'>";
    foreach($authors as $key=>$value) {
        $photo = "<span class='author-photo'></span>";
        if ($value['photo'] != '') $photo = "<img class='author-photo' src='".AUTHOR_PHOTO_FOLDER."{$value['photo']}'>";

        $is_click = '';
        $title = '';
        $allowDescribe = 0;
        if ($value['describe'] != '') {
            $is_click = ' click';
            $title="title='Дізнайтесь більше про цього автора'";
            $allowDescribe = 1;
        }

        $active_item = '';
        if ($value['author_id']==$current_author) $active_item = 'active-item';

        echo
            "<li class='author' id='author' data-describe='{$allowDescribe}'>".
            "<div id='author-describe' class='{$is_click}' data-author='{$value['author_id']}' {$title} >".
                "<span class='author-name {$active_item}'>{$describe}{$value['author']}</span>".
                "{$photo}".
            "</div>".
            "<a href='?author={$value['author_id']}' id='books-by-author' title='Книги цього автора'>".
                "<img class='books click' src='../assets/img/books1.png' class='click' >".
                "<span class='bage click'>{$value['cnt']}</span>".
            "</a>".
            "</li>".
            "<p id='author{$value['author_id']}' class='author-describe'>{$value['describe']}
            <img id='close-author-describe' class='close' src='../assets/img/close2.png'>
            </p>";

    }
    echo "</ul>";

    // список книг
    echo "<div id='book-list'>";
    echo "<ul>";
    foreach($books as $key=>$value){
        $photo = $value['picture'] != '' ? "{$photo_folder}{$value['picture']}" : '';
        if ($value['authorID'] == $current_author) 
        echo "<li>
        <span>{$value['book']}</span>
        <span>
            <img src='{$photo}'>
        </span>
        </li>";
    }
    echo "<ul>";
    echo "</div>";
    echo "</div>";
?>

<script>
    let openAuthor; // id відкритого блока 'про автора'

    let authorDescribeClick = (event)=>{ // обробниик клік по автору
        if (event.target.id == 'close-author-describe') event.target.parentElement.style.display = 'none';
    }
    document.querySelectorAll('.author-describe'). // вішаєм обробники авторів
        forEach( (i)=>{
            i.addEventListener('click', authorDescribeClick);
        });


    let authorClick = (event)=>{ // обробник кліку по книгах автора
        alert(event.currentTarget.id)
        if (event.currentTarget.id == 'books-by-author') return;
        if (openAuthor != null) document.querySelector(`#${openAuthor}`).style.display = 'none';
        if (event.currentTarget.dataset.describe == 1) {
            event.currentTarget.nextElementSibling.style.display = 'block';
            openAuthor = event.currentTarget.nextElementSibling.id;
        }
    };

    document.querySelectorAll('#author').
        forEach( (i)=>{
            i.addEventListener('click', authorClick);
        });

</script>
