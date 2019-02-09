<?
// require "php_ajax/connect.php";
$query = getQuery('admin','');
$content = mysql_fetch_assoc($query);
echo "<p>{$content['main']}</p>";
?>
