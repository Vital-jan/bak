<?
    const DB_HOST = 'localhost';
    const DB_NAME = 'db1';
    const DB_USER = 'root';
    const DB_PASS = '';
    const AUTHOR_PHOTO_FOLDER = '../assets/img/authors/';
    const BOOK_PHOTO_FOLDER = '../assets/img/books/';
    
    const ROOTFOLDER = "/bak/";
    
    const PHP_PATH = '/bak/php_ajax/'; // папка .php файлів та модулю mysqlajax.js

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

    function filename_parse($file) {
        if (!$file || $file == '') return false;
        $name = basename($file);
        $path = substr($file, 0, strlen($file) - strlen($name));
        $index = strpos($name, '.');
        if ($index === false) {
            $name = $file;
            $ext = '';
        }
        else {
            $exp = explode('.', $name);
            $ext = $exp[1];
            if ($ext) $ext = '.'.$ext;
            $name = $exp[0];
        }
        return array('path'=>$path, 'name'=>$name, 'ext'=>$ext);
    }

    function filename_generate($file) {
        if (!$file || $file == '') return false;
        $fa = filename_parse($file);
        if (!$fa) return false;
        
        $counter = 0;
        $new_file = $file;
        while (file_exists($file)) {
            $file = $fa['path'].$fa['name'].$counter.$fa['ext'];
            $counter++;
        }
        return $file;
    }

?>