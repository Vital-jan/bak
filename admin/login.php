<?
require $_SERVER['DOCUMENT_ROOT']."/bak/session.php";

require "../php_ajax/connect.php";
    $array = array('login'=>0);
    if (isset($_POST)) {
        $adm = mysql_fetch_assoc(mysql_query("SELECT * FROM admin"));
        $array['login'] = $adm['password'] == $_POST['body'];
        if ($array['login']) $_SESSION['login'] = true;
    }

    exit (json_encode($array));
?>