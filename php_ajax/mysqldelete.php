<?
require "connect.php";

$query = "DELETE FROM ";
$where = '';

foreach($_POST as $key=>$value) {

    if ($key == '$table') {
        $query .= $value;
        continue;
    }

    if ($key == '$where') {
        $where = ' WHERE '.$value;
        continue;
    }
}

 $query .= $where;
 $result=false;
//  exit (json_encode(array('Query:'=>$query)));
 if ($where !='') $result = mysqli_query($GLOBALS['db_connect'], $query);
 

$sql = array('sql'=>$result, 'query' => $query, 'error'=>mysqli_error($GLOBALS['db_connect']));
exit (json_encode($sql));
?>