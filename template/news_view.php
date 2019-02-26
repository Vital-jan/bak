<?
    $query = getQuery('news', 'order by date desc');
    $news = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $news[] = $cRecord;
    }

    $login = getLogin();

    if ($login) {
        echo "<button type='button'> Додати новину </button><br>"; 
    };

    foreach($news as $key=>$value) {
            $value[header] = strip_tags($value[header],'<br>');
            $value[date] = strip_tags($value[date],'<br>');
            $value[content] = strip_tags($value[content],'<br>');
            $btns = '';
            if ($login) $btns = "<img class='edit-button' src='../assets/img/edit-button.png'><img class='edit-button' src='../assets/img/close.png'>";
        echo "
            <div class='news-item'>
            <h2 class='news-header'>
            <span class='news-date'>{$value['date']}</span>
            {$btns}
            {$value['header']}
            </h2>
            <p class='news-content'>{$value['content']}</p>
            </div>
            ";
    }
?>

<script src='../assets/js/explorer.js'></script>
<script>
document.addEventListener("DOMContentLoaded", ()=>{

    let itemTimeout = 50;
    let newsList = document.querySelectorAll('.news-item');
    
    newsList.forEach((i)=>{
        setTimeout(()=>{
            fade(i, 300);
        }, itemTimeout);
        itemTimeout +=50;
    })
}) // onload
</script>