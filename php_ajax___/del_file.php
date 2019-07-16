<? // удаление файла с сервера
// принимает $_POST['file'] - имя удаляемого файла
// возвращает response.error = 0 или 1 (при неудачном удалении)
require 'connect.php';
$response = array('error'=> 0);

if (file_exists($_POST['file'])) {
    if (!unlink($_POST['file'])) $response['error'] = 1;
} else $response['error'] = 1;
exit (json_encode($response));
?>
