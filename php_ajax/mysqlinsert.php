<?
require 'connect.php';

$query = "INSERT INTO ";
$table = '';
$keys = "";
$table = '';
$values = "";

foreach($_POST as $key=>$value) {

    if ($key == '$table') {
        $query .= $value;
        $table = $value;
        continue;
    }

     if ($key[0] == '#') 
        {
            $value = is_numeric($value) ? $value : 'NULL';
            $key = substr($key, 1, strlen($key));
        }
        else {
            $value = str_check($value);
        }

        $values .= "'".$value."',";
        $keys .= $table.'.'.$key.',';
    }

 $values[strlen($values)-1] = ')';
 $values = "(".$values;

 $keys[strlen($keys)-1] = ')';
 $keys = "(".$keys;

 $query .= ' '.$keys.' VALUES '.$values;
 $result = false;
 $result = mysqli_query($GLOBALS['db_connect'], $query);
 
 $sql = array('sql'=>$result, 'query' => $query, 'error'=>mysqli_error($GLOBALS['db_connect']));
 exit (json_encode($sql));

?>