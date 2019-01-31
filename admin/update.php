<?
require '../template/connect.php';
$_POST['select']='admin';
if (isset($_POST)) {
    // Збереження контактів та "про нас" 
    if ($_POST['select'] == 'admin') {

        $_POST['address'] = str_check($_POST['address'],'<br>');
        $_POST['phone'] = str_check($_POST['phone']);
        $_POST['email'] = str_check($_POST['email']);
        $_POST['about'] = str_check($_POST['about']);
        mysql_query("UPDATE admin SET admin.address = '{$_POST['address']}', admin.phone = '{$_POST['phone']}', admin.email = '{$_POST['email']}', admin.about = '{$_POST['about']}'");
    }

    //
    if ($_POST['select'] == 'password') {};
    //
    if ($_POST['select'] == '') {};
    //
    if ($_POST['select'] == '') {};
    //
    if ($_POST['select'] == '') {};
    
}
?>