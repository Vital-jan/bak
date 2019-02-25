<?
    
    const DB_HOST = 'localhost';
    const DB_NAME = 'db1';
    const DB_USER = 'root';
    const DB_PASS = '';
    const AUTHOR_PHOTO_FOLDER = '../assets/img/authors/';
    const BOOK_PHOTO_FOLDER = '../assets/img/books/';
    
    const PHP_PATH = '/bak/php_ajax/'; // путь к php файлам и модулю mysqlajax.js

    const ROOTFOLDER = '/bak/';
    // в этой папке должны находиться файлы:
    // connect.php
    // mysqlajax.js
    // mysqlselect.php
    // mysqlupdate.php
    // mysqlinsert.php
    // mysqldelete.php
    // Данный файл применяется во всех модулях, использующих БД.

    $db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
    mysql_select_db(DB_NAME, $db);

    function getLogin() {
    if (isset($_SESSION)) {
        if ($_SESSION['login']) return true;
    }
    return false;
    }

    function getQuery($table, $query = '') {
    return mysql_query("SELECT * FROM {$table} {$query}", $GLOBALS['db']);
    }

    function get_array($query) {
        $array = array();
        while ($cRecord = mysql_fetch_assoc($query)) {
            $array[] = $cRecord;
        }
        return $array;
    }

?>