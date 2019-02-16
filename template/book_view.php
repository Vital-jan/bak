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
    <?
        if ($current_folder) echo "<a id='folder-list' href='.'><img src='../assets/img/books2.png'>Список розділів...</a>";
    ?>
    
    <div class="book-left">
        <ul>
            <? // відображення списку розділів
                foreach($folders as $key=>$value) { 
                    $active_class = $value['folder_id'] == $current_folder ? "class='active'" : '';
                    if (!$current_folder) $active_class = "class = 'visible'";
                    echo "
                    <a {$active_class} href='?folder={$value['folder_id']}'>
                    <li >
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
    
    <div class="book-right"> 
        <!-- <div id='book-list'> -->
            <? // відображення списку книг
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
        <!-- </div> -->
    </div>
</div>
<script>
    let currentBook;
    document.querySelector('.book-right').addEventListener('click',(event)=>{
        let el = event.target;
        while (!el.dataset.mainelement) el = el.parentElement;
        el.classList.toggle('book-view');
        if (currentBook) if (currentBook != el) currentBook.classList.remove('book-view');
        el.scrollIntoView();
        window.scrollBy(0, -200)
        currentBook = el;
    })
</script>