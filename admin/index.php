<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log-in page</title>
    <script src='../php_ajax/mysqlajax.js'></script>
    <script src='../assets/js/explorer.js'></script>
</head>
    <?
    require "../php_ajax/connect.php";
    ?>
<body>
    <form name='admin'>
        <span>Пароль:</span>
        <input type="password" name='password'>
        <br><br>
        <button id="login" type='button'>Log in</button>
        <br><br>
        <button id="remind" type='button'>Remind password</button>
    </form>
</body>
<script>
    function checkPassword(j) {
        if (j.login) {
            popUpWindow('Вітаємо! Авторизація успішна.', ()=>{document.location.href = '<?=ROOTFOLDER?>'});
        }
        else popUpWindow('Невірний пароль');
    }

    document.querySelector('#login').addEventListener('click', ()=>{
        if (document.forms.admin.password.value == '') return;
        ajax(document.forms.admin.password.value, checkPassword, 'login.php', '');
    })

    function remindPassword(j){
        if (j.send) {
            popUpWindow('Пароль надіслано на email, зазначений в розділі "Контакти".');
        }
        else popUpWindow('Помилка надсилання паролю');
    };

    document.querySelector('#remind').addEventListener('click', ()=>{
        ajax('', remindPassword, 'remind.php');
    })
</script>
</html>