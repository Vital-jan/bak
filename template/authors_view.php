<!DOCTYPE html>
<?
    const AUTHOR_PHOTO_FOLDER = '../assets/img/authors/';

    $query = getQuery('authors');
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
            "<img src='../assets/img/books1.png' class='click' title='Книги цього автора'>".
            "</li>".
            "<li class='author'>".
            "<p class='author-describe'>{$value['describe']}</p>".
            "</li>"            
            ;
    }
    echo "</ul>";

?>
