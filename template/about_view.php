<?
$query = getQuery('admin','');
$contacts = mysql_fetch_assoc($query);
// $login = getLogin();
?>
<div class="main-content">
    <p>
    <?if ($login):?>
        <img class='edit-button' id='edit' src='../assets/img/edit-button.png'>
    <?endif;?>

    <?=$contacts['about']?>
    </p>
</div>
<script src='../assets/js/explorer.js'></script>
<script>
document.addEventListener("DOMContentLoaded", ()=>{
    fade(document.querySelector('.main-content'), 300);

    if (document.querySelector('#edit')) document.querySelector('#edit').addEventListener('click', (event)=>{ // кнопка редагування
        modalWindow('Сторінка <Про нас>. Редагування.',
        '<textarea id="main-content" style="width:100%; height:50%;"><?=$contacts['about']?></textarea>',
        ['+Зберегти', '-Скасувати'],
        (n)=>{
            if (n != 0) return;
            // збереження контенту в базі
            queryUpdate('admin', `admin_id=1`,
            [
            ['about', document.querySelector('#main-content').value],
            ], 
            (response)=>{if (!response.sql) {alert('Помилка! Інформацію не збережено!')} else location.reload(true)}, '<?=PHP_PATH?>');
        },
        '80%', 300);
    });
})
</script>