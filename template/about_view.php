<!DOCTYPE html>
<?
$query = getQuery('admin','');
$contacts = mysql_fetch_assoc($query);
$login = getLogin();
?>
<div class="main-content">
    <p>
    <?if ($login):?>
        <img class='edit-button' id='edit' src='../assets/img/edit-button.png'>
    <?endif;?>

    <?=$contacts['about']?>
    </p>
</div>
