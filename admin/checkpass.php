<?
    require "../php_ajax/connect.php";
    $array = array('confirm'=>0);
    if (isset($_POST)) {
        $adm = mysql_fetch_assoc(mysql_query("SELECT admin.password FROM admin"));
        $array['confirm'] = $adm['password'] == $_POST['body'];
    }

    exit (json_encode($array));
?>