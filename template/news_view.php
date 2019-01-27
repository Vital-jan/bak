<!DOCTYPE html>
<?
    $db = mysql_connect("localhost","root","");
    mysql_select_db("db1",$db);
    $query = mysql_query("SELECT header FROM news",$db);
    $news = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $news[] = $cRecord;
    }
    
    foreach($news as $key=>$value) {
        echo "<h2 class='news-header'>{$value['header']}</h2>";
    }
?>

