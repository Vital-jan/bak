<!DOCTYPE html>
<?
require 'connect.php';
$query = getQuery('admin','');
$contacts = mysql_fetch_assoc($query);
echo "<p>{$contacts['about']}</p>";
?>
