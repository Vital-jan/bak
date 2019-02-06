<!DOCTYPE html>
<?
    const AUTHOR_PHOTO_FOLDER = '../assets/img/authors/';
	require "../php_ajax/connect.php";
    $query = mysql_query("SELECT authors.author, Count(*) AS cnt, bookauthor.book, books.book, authors.photo, authors.describe FROM
    authors INNER JOIN bookauthor ON authors.author_id = bookauthor.author INNER JOIN books ON bookauthor.book = books.book_id
    group by authors.author_id");

    $authors = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $authors[] = $cRecord;
    }

    echo "${AUTHOR_PHOTO_FOLDER}";
    echo "<ul>";
    foreach($authors as $key=>$value) {
        $photo = '';
        if ($value['photo'] != '') $photo = "<img class='author-photo' src='".AUTHOR_PHOTO_FOLDER."{$value['photo']}'>";
        $describe = '';
        if ($value['describe'] != '')
            $describe = "<button title='Дізнатись більше про цього автора'>...</button>";
        echo
            "<li class='author'>".
            "<span class='author-name'>{$value['author']}{$describe}</span>".
            "<div class='author-photo-frame'>{$photo}</div>".
            "<img class='books click' src='../assets/img/books1.png' class='click' title='Книги цього автора'>".
            "<span class='bage click' title='Книги цього автора'>{$value['cnt']}</span>".
            "</li>".
            "<li class='author'>".
            "<p class='author-describe'>{$value['describe']}</p>".
            "</li>"            
            ;
    }
    echo "</ul>";

?>
