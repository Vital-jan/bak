<?
$query = getQuery('admin','');
$content = mysql_fetch_assoc($query);
$query = getQuery('news', 'order by date desc limit 3');
$news = array();

while ($cRecord = mysql_fetch_assoc($query)) {
    $news[] = $cRecord;
}

$query = mysql_query("SELECT picture FROM books WHERE deleted = 0 ORDER BY RAND() LIMIT 3");
$books = array();
while ($cRecord = mysql_fetch_assoc($query)) {
    $books[] = $cRecord;
    }
?>
<div class='main-content'>
    <div>
        <p>
            <?=$content['main']?>
        </p>
            <img src='assets/img/book_bl.jpg'>
    </div>
<div id="pages">
<div class="page">
    <div class="page-in"><h5><?=strip_tags($news[2]['date'])?></h5><span><?=strip_tags($news[2]['content'])?></span></div>
    <div class="page-in"><h5><?=strip_tags($news[1]['date'])?></h5><span><?=strip_tags($news[1]['content'])?></span></div>
    <div class="page-in"><h5><?=strip_tags($news[0]['date'])?></h5><span><?=strip_tags($news[0]['content'])?></span></div>
</div>
<div class="page">
    <div class="page-in-right"><img src='assets/img/books/<?=$books[2]['picture']?>'></div>
    <div class="page-in-right"><img src='assets/img/books/<?=$books[1]['picture']?>'></div>
    <div class="page-in-right"><img src='assets/img/books/<?=$books[0]['picture']?>'></div>
</div>
</div>
</div>
