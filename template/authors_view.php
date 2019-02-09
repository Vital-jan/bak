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

        $describe_tag = '';
        if ($value['describe'] != '') {
            $describe_tag = "id='author-describe' style='cursor: pointer' title='Дізнайтесь більше про цього автора'";
        }

        $active_item = '';
        if ($value['author_id']==$current_author) $active_item = 'active-item';

        echo
            "<li class='author' id='author'>".
            "<div {$describe_tag} class='author-describe' data-author='{$value['author_id']}'>".
                "<span class='author-name {$active_item}'>{$describe}{$value['author']}</span>".
                "{$photo}".
            "</div>".
            "<a href='?author={$value['author_id']}' id='books-by-author' title='Книги цього автора'>".
                "<img class='books click' src='../assets/img/books1.png' class='click' >".
                "<span class='bage click'>{$value['cnt']}</span>".
            "</a>".
            "</li>".
            "<p id='describe-text{$value['author_id']}' class='describe-text'>{$value['describe']}
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
        <span>&laquo;{$value['book']}&raquo;</span>
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

    document.querySelector('#author-list').addEventListener('click', (event)=>{ // вішаємо обробник кліку по автору
        if ((event.target.id || event.target.parentElement.id) == 'author-describe') {
            if (openAuthor) openAuthor.style.display = 'none';
            let id = event.target.dataset.author || event.target.parentElement.dataset.author;
            openAuthor = document.querySelector(`p#describe-text${id}`);
            openAuthor.style.display = 'block';
        }
        if (event.target.id == 'close-author-describe') event.target.parentElement.style.display = 'none';
    });

</script>
