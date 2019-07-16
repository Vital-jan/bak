<?
require "../session.php";
require "../php_ajax/connect.php";
$ret = array('login'=>0);
if (isset($_POST)) {
    $adm = mysqli_fetch_assoc(mysqli_query($GLOBALS['db_connect'], "SELECT * FROM admin"));
    $ret['login'] = $adm['password'] == $_POST['body'];
    if ($ret['login']) $_SESSION['login'] = true;
}

exit (json_encode($ret));
?>