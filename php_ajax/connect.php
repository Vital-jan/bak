<?
    const DB_HOST = 'localhost';
    const DB_NAME = 'db1';
    const DB_USER = 'root';
    const DB_PASS = '';
    const AUTHOR_PHOTO_FOLDER = '../assets/img/authors/';
    const BOOK_PHOTO_FOLDER = '../assets/img/books/';
    
    const PHP_PATH = '/bak/php_ajax/'; // папка .php файлів та модулю mysqlajax.js

    const ROOTFOLDER = "/bak/";
    // в цій папці повинні знаходитись:
    // connect.php
    // mysqlajax.js
    // mysqlselect.php
    // mysqlupdate.php
    // mysqlinsert.php
    // mysqldelete.php
    // Цей файл під'єднується у всіх модулях, що використовують БД.

    $db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
    mysql_select_db(DB_NAME, $db);

    function str_check($str){ // валідація тексту перед збереженням в БД
        $str = str_replace("<br/>", "\n", $str); 
        $str = str_replace("&", "~~", $str); 
        $str = strip_tags($str);
        $str = htmlspecialchars ($str, ENT_QUOTES);
        $str = str_replace("`", "&#96;", $str); 
        $str = trim($str);
        $str = str_replace("\n", "<br/>", $str); 
        $str = str_replace("~~", "&", $str); 
        return $str;
    }

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