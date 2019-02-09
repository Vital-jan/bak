<!DOCTYPE html>
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

?>
<div class="books">
    <div class="book-left">
        <ul class='book-folder'>
            <? // відображення списку розділів
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
        <ul class="book-list">
            <? // відображення списку книг
            if (isset($books)) {

            $photo_folder = BOOK_PHOTO_FOLDER;
            foreach($books as $key=>$value){
                if ($value['folder'] == $current_folder) 
                {
                    $book_picture = $value['picture'] != '' ? "<img src='{$photo_folder}{$value['picture']}'>" : '';
                    echo "<li data-mainelement='1' data-book='{$value['book_id']}'>
                    <div>
                    <span class='book-name'>&laquo;{$value['book']}&raquo; </span>
                    <span class='book-author'> <img class='writer' src='../assets/img/pero.jpg'> {$value['assemble']} </span>
                    <span class='book-describe'>{$value['describe']}</span>
                    </div>
                    <span class='img'> {$book_picture} </span>
                    </li>";
                    // echo "<p class='book-view'>
                    // <h3>{$value['book']}</h3>
                    // <h4></h4>
                    // </p>";
                }
                }
            }
            ?>
        </ul>
    </div>
</div>
<script>
    let currentBook;
    document.querySelector('.book-list').addEventListener('click',(event)=>{
        if (currentBook) currentBook.classList.toggle('book-view');
        let el = event.target;
        while (!el.dataset.mainelement) el = el.parentElement;
        if (el == currentBook) return;
        el.classList.toggle('book-view');
        currentBook = el;
    })
</script>