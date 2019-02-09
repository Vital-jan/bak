<!DOCTYPE html>
<?
// require "../php_ajax/connect.php";

    $query = getQuery('news', 'order by date desc');
    $news = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $news[] = $cRecord;
    }
    
    foreach($news as $key=>$value) {
            $value[header] = strip_tags($value[header],'<br>');
            $value[date] = strip_tags($value[date],'<br>');
            $value[content] = strip_tags($value[content],'<br>');
        echo "
            <h2 class='news-header'>
            <span class='news-date'>{$value['date']}</span>
            {$value['header']}
            </h2>
            <p class='news-content'>{$value['content']}</p>
            </div>
            ";
    }
?>

