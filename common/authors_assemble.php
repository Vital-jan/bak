<?
    // перелік авторів через кому для кожної книги
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