<?
require '../template/connect.php';
if (isset($_POST)) {
    // Збереження контактів та "про нас" 
    if ($_POST['select'] == 'admin') {
        $_POST['company'] = str_check($_POST['company'],'<br>');
        $_POST['address'] = str_check($_POST['address'],'<br>');
        $_POST['phone'] = str_check($_POST['phone']);
        $_POST['email'] = str_check($_POST['email']);
        $_POST['about'] = str_check($_POST['about']);
        $_POST['main'] = str_check($_POST['main']);
        $_POST['footer'] = str_check($_POST['footer']);
        mysql_query("UPDATE admin SET 
            admin.address = '{$_POST['address']}',
            admin.phone = '{$_POST['phone']}',
            admin.email = '{$_POST['email']}',
            admin.main = '{$_POST['main']}',
            admin.footer = '{$_POST['footer']}',
            admin.about = '{$_POST['about']}',
            admin.company = '{$_POST['company']}'
            ");
    }

    //
    if ($_POST['select'] == 'password') {};

    // Збереження книги
    if ($_POST['select'] == 'book') {
        $_POST['book'] = str_check($_POST['book']);
        $_POST['describe'] = str_check($_POST['describe'],'<br>');
        $_POST['picture'] = str_check($_POST['picture']);
        $_POST['price'] = str_check($_POST['price'],'<br>');
        $price = is_numeric($_POST['price']) == 1 ? $_POST['price'] : 'NULL';
        $query = "UPDATE books SET 
            books.book = '{$_POST['book']}',
            books.describe = '{$_POST['describe']}',
            books.price = {$price},
            books.picture = '{$_POST['picture']}',
            books.available = '{$_POST['available']}',
            books.folder = '{$_POST['folder']}'
            where book_id = {$_POST['book_id']}
            ";
        $result = mysql_query($query);
        $sql = array('sql'=>$result, 'query' => $query);
        exit (json_encode($sql));
    };

    //
    if ($_POST['select'] == '') {};

    //
    if ($_POST['select'] == '') {};
    
}
?>