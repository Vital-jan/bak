<?
require "connect.php";

$array = array();
$query = mysqli_query($GLOBALS['db_connect'], $_POST['body']);
while ($cRecord = mysqli_fetch_assoc($query)) {
    $array[] = $cRecord;
}

exit (json_encode($array));
?>