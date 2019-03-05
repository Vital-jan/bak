<?
require 'connect.php';


 function str_check($str){
    $str = strip_tags($str, '<br>');
    $str = htmlspecialchars ($str);
    $str = trim($str);
    $str = str_replace("\n", "<br/>", $str); 
    return $str;
}

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
 $result = mysql_query($query);
 
 $sql = array('sql'=>$result, 'query' => $query, 'error'=>mysql_error());
 exit (json_encode($sql));

?>