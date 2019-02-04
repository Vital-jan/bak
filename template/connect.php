<?
    const DB_HOST = 'localhost';
    const DB_NAME = 'db1';
    const DB_USER = 'root';
    const DB_PASS = '';

    $db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
    mysql_select_db(DB_NAME, $db);

    // function getQuery($table, $query = '') {
    // return mysql_query("SELECT * FROM {$table} {$query}", $GLOBALS['db']);
    // }

    // function get_array($query) {
    //     $array = array();
    //     while ($cRecord = mysql_fetch_assoc($query)) {
    //         $array[] = $cRecord;
    //     }
    //     return $array;
    // }

?>