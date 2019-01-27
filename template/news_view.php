<!DOCTYPE html>
<?
    require 'connect.php';
    $query = getQuery('news');
    $news = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $news[] = $cRecord;
    }
    
    foreach($news as $key=>$value) {
        echo
            "<h2 class='news-header'>{$value['header']}</h2>".
            "<p class='news-date'>{$value['date']}</p>".
            "<p class='news-content'>{$value['content']}</p>";
    }
?>

