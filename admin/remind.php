<?php
require "../php_ajax/connect.php";

$psw ='';
$mail_to = '';
    $query = mysqli_query($GLOBALS['db_connect'], 'SELECT * FROM admin');
    $contacts = mysqli_fetch_assoc($query);
$psw =$contacts['password'];
$mail_to = $contacts['email'];

$success = array('send' => false);
if (mail($mail_to, "Нагадування паролю bak.lviv.ua", "Пароль: ".$psw."\r\nНе відповідайте на це повідомлення.\r\nЗ метою підвищення конфіденційності видаліть це повідомлення з Вашої пошти (а також з кошика) одразу ж, як запам'ятаєте пароль.", "From: bak.lviv.ua")) {$success['send'] = true;};

exit (json_encode($success));
?>