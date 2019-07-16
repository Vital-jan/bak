<?
require 'connect.php';

//  function str_check($str){
//     $str = str_replace("<br/>", "\n", $str); 
//     $str = str_replace("`", "'", $str); 
//     $str = str_replace("&", "~~", $str); 
//     $str = strip_tags($str);
//     $str = htmlspecialchars ($str, ENT_QUOTES);
//     $str = trim($str);
//     $str = str_replace("\n", "<br/>", $str); 
//     $str = str_replace("~~", "&", $str); 
//     return $str;
// }

$query = "UPDATE ";
$where = "";
$table = '';
$n = 0;

foreach($_POST as $key=>$value) {

    if ($key == '$table') {
        $query .= $value.' SET ';
        $table = $value;
        $n++;
        continue;
    }

    if ($key == '$where') {
        $where = ' WHERE '.$value;
        $n++;
        continue;
    }

     if ($key[0] == '#') {
        $value = is_numeric($value) ? $value : 'NULL';
        $key = substr($key, 1, strlen($key));
        $query .= "{$table}.{$key} = {$value}";
     }
     
     else {
        $value = str_check($value);
        $query .= "{$table}.{$key} = '{$value}'";
     }

     if ($n+1 < count($_POST)) $query .= ', ';
     $n++;
 }

 $query .= $where;
 $result=false;
 if ($where !='') $result = mysql_query($query);
 
 $sql = array('sql'=>$result, 'query' => $query, 'error'=>mysql_error());
 exit (json_encode($sql));

?>