<?
$query = getQuery('admin','');
$content = mysql_fetch_assoc($query);
echo "<p>{$content['main']}</p>";
?>
