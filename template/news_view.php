<?
    $query = getQuery('news', 'order by date desc');
    $news = array();
    
    while ($cRecord = mysql_fetch_assoc($query)) {
        $news[] = $cRecord;
    }

    $login = getLogin();

    if ($login) {
        echo "<button type='button' id='news-add'> Додати новину </button><br>"; 
    };
    foreach($news as $key=>$value) {
            $value[header] = strip_tags($value[header],'<br>');
            $value[date] = strip_tags($value[date],'<br>');
            $value[content] = strip_tags($value[content],'<br>');
            $btns = '';
            if ($login) $btns = "<img class='edit-button' id='news-edit' data-id={$value['news_id']} src='../assets/img/edit-button.png'><img class='edit-button' id='news-del' data-id={$value['news_id']} src='../assets/img/close.png'>";
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

function addEdit(item) { // редагування та додавання запису (item == null: додавання, item!=null - редагування, де item - ідентифікатор - ключ БД)

    function addEditForm(header, number = null) { // форма редагування - додавання
            let date = new Date();
            let cDate = `${date.getFullYear()}-${digitalForward(date.getMonth(),2)}-${digitalForward(date.getDate(),2)}`;
			modalWindow(header, `
			<form name='addEdit'  class="admin">
			<ul>
				<li><input type='date' value='${cDate}' name='date'></li>
				<li><input type='text' placeholder='Заголовок' name='header'></li>
                <li><textarea name='content' rows='3' placeholder='Контент'></textarea></li>
                <li><button type='button'>Обрати зображення</button></li>
                <li><button type='button'>Завантажити зображення</button></li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addEdit;
				if (btn == 0) { // збереження форми в базі
					if (!item) { // додаваня запису
					queryInsert('news', [
						['date', formAdmin.date.value],
						['header', formAdmin.header.value],
						['content', formAdmin.content.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис додано.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // додаваня запису
					if (item != null) { // редагування запису
					queryUpdate('news', `news_id=${item}`, [
						['date', formAdmin.date.value],
						['header', formAdmin.header.value],
						['content', formAdmin.content.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис змінено.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // редагування запису

				} // збереження форми в базі
			},
			'80%', 300); // modalwindow

        } // addEditForm
        
        if (item == null) { // додавання новини
			addEditForm ('Додати новину');
		}
		else { // редагування новини
            addEditForm('Редагувати новину', item);
            let formAdmin = document.forms.addEdit;
			queryGet(`select * from news where news_id=${item}`, (response)=>{ // отримуємо елемент з бази
                // наповнюємо поля форми
				formAdmin.date.value = response[0].date;
				formAdmin.header.value = htmlEncode(response[0].header);
				formAdmin.content.value = htmlEncode(response[0].content);
			}, '<?=PHP_PATH?>')
		}

}

	document.body.addEventListener('click', (event)=>{ // обробка кліку edit, del та add
		if (event.target.id == 'news-add') {
			addEdit(null);
		}
		if (event.target.id == 'news-edit') {
			addEdit(event.target.dataset.id);
			}
		if (event.target.id == 'news-del') { // видалення новини
			modalWindow('Видалення елементу', 'Ви підтверджуєте видалення цього елементу?', ['Залишити', '-Видалити'], (n)=>{
				if (n == 1) {
					queryDelete('news', `news_id=${event.target.dataset.id}`, (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис видалено.');
							document.location.reload(true);
						}
					}, '<?=PHP_PATH?>');
				}
			}, '60%');
		};
	})

}) // onload
</script>