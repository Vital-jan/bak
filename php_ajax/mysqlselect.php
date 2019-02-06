<?
require "connect.php";

$array = array();
$query = mysql_query($_POST['body']);
while ($cRecord = mysql_fetch_assoc($query)) {
    $array[] = $cRecord;
}

exit (json_encode($array));
?>