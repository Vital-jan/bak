<!DOCTYPE html>
<?
    $current_folder = $_GET['folder'];

    // книги
    $query = mysql_query("SELECT * FROM books WHERE deleted = 0 ORDER BY book");
    $books = array();
    while ($cRecord = mysql_fetch_assoc($query)) {
        $books[] = $cRecord;
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

?>
<div class="books">
    <div class="book-left">
        <ul class='book-folder'>
            <?
                foreach($folders as $key=>$value) {
                    $active_class = $value['folder_id'] == $current_folder ? "class='active'" : '';
                    echo "
                    <a {$active_class} href='?folder={$value['folder_id']}'>
                    <li >
                    <span>
                    {$value['folder']}
                    </span>
                    <span class='bage'>{$value['cnt']}</span>
                    </li></a>";
                }
            ?>
        </ul>
    </div>
    <div class="book-right">
        <ul class = "book-list">
            <?
            $photo_folder = BOOK_PHOTO_FOLDER;
            foreach($books as $key=>$value){
                if ($value['folder'] == $current_folder) 
                {
                    $book_picture = $value['picture'] != '' ? "<img src='{$photo_folder}{$value['picture']}'>" : '';
                    echo "<li>
                    <div>
                    <span class='book-name'>{$value['book']} </span>
                    <span class='book-author'> {$value['assemble']} </span>
                    </div>
                    <span class='img'> {$book_picture} </span>
                    </li>";
                }
            }
            ?>
        </ul>
    </div>
</div>