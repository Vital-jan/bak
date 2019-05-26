<? // Загрузка графического файла на сервер
require 'connect.php';
 $response = array('filename'=>$_FILES['file']['name'], 'error'=>0, 'upload'=>false);
//  проверка размера и типа файла
//  error = 1: непр. размер
//  error = 2: непр. формат
 if ($_FILES['file']['size'] > $_POST['size'] && $_POST['size'] > 0) $response['error'] = 1;
 if (substr($_FILES['file']['type'], 0, 5) != 'image') $response['error'] = 2;
 
if ($response['error'] == 0 && is_uploaded_file($_FILES['file']['tmp_name'])) {
    $new_file = trim($_FILES['file']['name']);
    $new_file = $_SERVER['DOCUMENT_ROOT'].ROOTFOLDER.$_POST['path'].$new_file;
    $new_file = filename_generate($new_file);
    $fn = filename_parse($new_file);
    $response['filename'] = $fn['name'].$fn['ext'];
    $response['upload'] = move_uploaded_file($_FILES['file']['tmp_name'], $new_file);
    $response['fullpath'] = $_SERVER['DOCUMENT_ROOT'].ROOTFOLDER.$_POST['path'].$new_file;
}

exit (json_encode($response));
?>