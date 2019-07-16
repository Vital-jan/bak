<?
    require "../php_ajax/connect.php";
    $array = array('confirm'=>0);
    if (isset($_POST)) {
        $adm = mysqli_fetch_assoc(mysqli_query($GLOBALS['db_connect'], "SELECT admin.password FROM admin"));
        $array['confirm'] = $adm['password'] == $_POST['body'];
    }

    exit (json_encode($array));
?>