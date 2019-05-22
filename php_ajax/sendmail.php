<?php
$response = array('send' => false, 'error'=> '');
if (!isset($_POST)) {
    $response['error'] = 'Required $_POST parameters for send mail';
    exit (json_encode($response));
}

$from = 'From: '.$_POST['from'];
if (mail($_POST['mail_to'], $_POST['subj'], $_POST['message'], $from)) {$response['send'] = true;};

exit (json_encode($response));
// возвращает json response.send = true/false, error содержит пустую строку или ошибку.
// параметр $_POST['from'] передает заголовок "от кого", отображаемый в почтовом клиенте. Ключевое слово From: добавляется автоматически.
?>