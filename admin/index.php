<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../assets/admin.css">
    <title>Admin</title>
</head>
<body>
    <?
    require "../template/connect.php";
    $admin = get_array(mysql_query('SELECT * FROM admin'));
    ?>

    <form class="main-admin" name="admin">
        <div class="left">
            <h2>Контакти:</h2>
            <br>
            Адреса:<br>
            <textarea rows="2" placeholder="Адреса" name="address"></textarea><br>
            Телефон:<input type="text" placeholder="Телефон" name="phone"><br>
            Email:<input type="text" placeholder="Email" name="email"><br>
            <br>
            <h2>Про нас:</h2>
            <small>Розділ 'Про нас' може вміщувати до 1000 символів. Дозволений HTML тег &lt;br&gt; для переносу тексту.</small><br>
            <small id="strlen"></small><br>
            <textarea id="text" rows="10" name="about"></textarea><br>
            <br>
            <button type="button" id="save">Зберегти зміни</button>
            <br><br>
            <hr>
            <button type="button" id="new-password-btn">Змінити пароль</button>
            <div id="new-password">
                <input type="password" id="password1" placeholder="Новий пароль" name="pass1"><br>
                <input type="password" id="password2" placeholder="Підтвердження паролю" name="pass2"><br>
                <button type="button">Підтвердити новий пароль</button>
            </div>
        </div>
        <div class="right">
            <h2>Бази даних:</h2>
            <a href="edit_book.php"><button type="button">Книги</button></a>
            <br>
            <button type="button">Розділи</button>
            <br>
            <button type="button">Автори</button>
            <br>
            <button type="button">Зображення</button>
            <br>
        </div>
    </form>
    <script>
        let form = document.forms.admin.elements;
        form.address.value = "<?=addslashes($admin[0]['address'])?>";
        form.phone.value = "<?=addslashes($admin[0]['phone'])?>";
        form.email.value = "<?=addslashes($admin[0]['email'])?>";
        form.about.value = "<?=addslashes($admin[0]['about'])?>";

        
    window.onbeforeunload = function() {
        return "Перш ніж залишити цю строінку, перевірте чи збережені зміни.";
    };

    document.querySelector('#new-password-btn').addEventListener('click',()=>{
        document.querySelector('#new-password').style.display = 'block';
    })

    let strlen = document.querySelector('#strlen');
    document.querySelector('#text').addEventListener('keyup', (event)=>{
        strlen.innerHTML = `Лишилось: ${1000-event.target.value.length} символів`;
        strlen.style.color = event.target.value.length > 980 ? 'red' : 'inherit';
    })

    document.querySelector('#save').addEventListener('click',()=>{

        let data = new FormData;
        data.append('select','admin');
        data.append('address',form.address.value);
        data.append('phone',form.phone.value);
        data.append('email',form.email.value);
        data.append('about',form.about.value);

        function status(response) {  
          if (response.status == 200) {  
            return Promise.resolve(response)  
          } else {  
            return Promise.reject(new Error(response.statusText))  
          }  
        }

        fetch('update.php', {
          method: "POST",
          body: data
        }) 
          .then(status)
          .catch(function(error) {  
          });
        })
    </script>
</body>
</html>