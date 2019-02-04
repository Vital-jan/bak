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
            Компанія:<input type="text" placeholder="Назва компанії" name="company" value="<?=$admin[0]['company']?>"><br>
            Адреса:<br>
            <textarea rows="2" placeholder="Адреса" name="address"><?=$admin[0]['address']?>"></textarea><br>
            Телефон:<input type="text" placeholder="Телефон" name="phone" value="<?=$admin[0]['phone']?>"><br>
            Email:<input type="text" placeholder="Email" name="email" value="<?=$admin[0]['email']?>"><br>
            <br>
            <h2>Головна сторінка:</h2>
            <small>Розділ може вміщувати до 1000 символів. Дозволений HTML тег &lt;br&gt; для переносу тексту.</small><br>
            <small id="strlen1"></small><br>
            <textarea id="text1" rows="10" name="main" placeholder="Текст головної сторінки"><?=$admin[0]['main']?></textarea><br>
            <br>
            <h2>Про нас:</h2>
            <small>Розділ 'Про нас' може вміщувати до 1000 символів. Дозволений HTML тег &lt;br&gt; для переносу тексту.</small><br>
            <small id="strlen"></small><br>
            <textarea id="text" rows="10" name="about" placeholder="Текст сторінки 'Про нас'"><?=$admin[0]['about']?></textarea><br>
            Footer:<input type="text" placeholder="Footer" name="footer" value="<?=$admin[0]['footer']?>"><br>
        </div>
        <div class="right">
            <h2>Бази даних:</h2>
            <a href="edit_book.php"><button type="button">Книги</button></a>
            <br>
            <button type="button">Розділи</button>
            <br>
            <button type="button">Автори</button>
            <br>
            <button type="button">Новини</button>
            <br>
            <button type="button">Де придбати</button>
            <br>
            <button type="button">Зображення</button>
            <br><br>
            <br>
            <button type="button" id="save">Зберегти зміни</button>
            <button type="button" id="undo">Скасувати зміни</button>
            <br><br>
            <hr>
            <button type="button" id="new-password-btn">Змінити пароль</button>
            <div id="new-password">
                <input type="password" id="password1" placeholder="Новий пароль" name="pass1"><br>
                <input type="password" id="password2" placeholder="Підтвердження паролю" name="pass2"><br>
                <button type="button">Підтвердити новий пароль</button>
            </div>

        </div>
    </form>
    <script>
        let form = document.forms.admin.elements;

    window.onbeforeunload = function() {
        return "Перш ніж залишити цю строінку, перевірте чи збережені зміни.";
    };

    document.querySelector('#new-password-btn').addEventListener('click',()=>{
        document.querySelector('#new-password').style.display = 'block';
    });

    let strlen = document.querySelector('#strlen');
    document.querySelector('#text').addEventListener('keyup', (event)=>{
        strlen.innerHTML = `Лишилось: ${1000-event.target.value.length} символів`;
        strlen.style.color = event.target.value.length > 980 ? 'red' : 'inherit';
    });

    let strlen1 = document.querySelector('#strlen1');
    document.querySelector('#text1').addEventListener('keyup', (event)=>{
        strlen1.innerHTML = `Лишилось: ${1000-event.target.value.length} символів`;
        strlen1.style.color = event.target.value.length > 980 ? 'red' : 'inherit';
    });

    document.querySelector('#undo').addEventListener('click',()=>{
        refresh();
    });

    document.querySelector('#save').addEventListener('click',()=>{

        let data = new FormData;
        data.append('$table','admin');
        data.append('$where','admin_id=1');
        data.append('company',form.company.value);
        data.append('address',form.address.value);
        data.append('phone',form.phone.value);
        data.append('email',form.email.value);
        data.append('about',form.about.value);
        data.append('main',form.main.value);
        data.append('footer',form.footer.value);

        function status(response) {  
          if (response.status == 200) {
              
            return Promise.resolve(response)  
          } else {  
            return Promise.reject(new Error(response.statusText))  
          }  
        }
        function json(response) {
          return response.json()
        }
            
        fetch('mysqlupdate.php', {
          method: "POST",
          body: data
        }) 
          .then(status)
          .then(json)
          .then(function(phpJSON){
            if (!phpJSON.sql) alert('Помилка! Інформація не збережена.'+phpJSON.query+phpJSON.error); else alert('Інформація збережена.');
          })
          .catch(function(error) {  
          });
        })
    </script>
</body>
</html>