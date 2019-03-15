<?
require 'connect.php';
 $response = array('filename'=>$_FILES['file']['name'], 'error'=>0, 'upload'=>false, 'type'=>$_POST['type']);
 // проверка размера и типа файла
 if ($_FILES['size'] > $_POST['size'] && $_POST['size'] > 0) $response['error'] = 1;
 if ($_FILES['type'] != $_POST['type'] && $_POST['type'] != '') $response['error'] = 2;
 
if ($response['error'] == 0 && is_uploaded_file($_FILES['file']['tmp_name'])) {
    $response['upload'] = move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].ROOTFOLDER.'uploads/'.$_FILES['file']['name']);
}

exit (json_encode($response));
?>