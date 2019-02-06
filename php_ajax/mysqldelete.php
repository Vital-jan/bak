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
 if ($where !='') $result = mysql_query($query);
 

$sql = array('sql'=>$result, 'query' => $query, 'error'=>mysql_error());
exit (json_encode($sql));
?>