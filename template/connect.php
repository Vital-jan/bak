<?
    const DB_HOST = 'localhost';
    const DB_NAME = 'db1';
    const DB_USER = 'root';
    const DB_PASS = '';

    function getQuery($table, $query = '') {
        $db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
        mysql_select_db(DB_NAME, $db);
    return mysql_query("SELECT * FROM {$table} {$query}", $db);
    }
?>