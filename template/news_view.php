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
            $btns = '';
			if ($login) $btns = "<img class='edit-button' id='news-edit' data-id={$value['news_id']} src='../assets/img/edit-button.png'><img class='edit-button' id='news-del' data-id={$value['news_id']} src='../assets/img/close.png'>";
			$path = NEWS_PHOTO_FOLDER.$value['picture'];
			$img = ($value['picture'] != '' && $value['picture'] != null) ? "<img class='news-img' id='news-img' src='{$path}'>" : '';
        echo "
			<div class='news-item'>
			{$img}
			<h2 class='news-header'>
            <span class='news-date'>{$value['date']}</span>
            {$btns}
            {$value['header']}
            </h2>
			<p class='news-content'>
				{$value['content']}
			</p>
            </div>
            ";
	}
	
    // визначаємо зображення, пов'язані з новинами
    $bookpict = array();
    foreach($news as $key=>$value) {
        $bookpict[$value['picture']] = $value['content'] ? true : false;
    }

	// завантажуємо каталог зображень новин
    $pictures = scandir(NEWS_PHOTO_FOLDER);
    array_shift($pictures);
    array_shift($pictures);
    $picture_list = "<div id='picture-list'>";
    $path = NEWS_PHOTO_FOLDER;
    foreach($pictures as $value) {
        $del = '';
        if (!$bookpict[$value]) $del = "<img data-id='del' data-file='{$value}' class='del-picture' src='../assets/img/close.png'>";
        $picture_list .= "<div><img src='{$path}{$value}'>${del}</div>";
    }
    $picture_list .= "</div>";

?>

<script src='../assets/js/explorer.js'></script>
<script>
document.addEventListener("DOMContentLoaded", ()=>{

	document.body.addEventListener('click', (event)=>{
		if (event.target.id == 'news-img') event.target.classList.toggle('news-img-large');
	})

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
				<li>
				<?=$picture_list?>

				<img id="img-book" class="book-img" src="">

                    Зображення: <input type='text' disabled='' name='picture' style='width:15%'>
                    <button type='button' id='picture-clear'>Очистити зображення</button>
					<button type='button' id='picture-choice'>Обрати зображення</button>
					<br>
                    <label class='button'>
                        Завантажити зображення
                        <input type='file' id='picture-upload' name="file" accept='image/*'></input>
                    </label>
                    <span class='wait'>Uploading... <img src='../assets/img/book.gif'></span>
                </li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addEdit;
				if (btn == 0) { // збереження форми в базі
					if (!item) { // додаваня запису
					queryInsert('news', [
						['date', formAdmin.date.value],
						['header', formAdmin.header.value],
						['content', formAdmin.content.value],
						['picture', formAdmin.picture.value]
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
						['content', formAdmin.content.value],
						['picture', formAdmin.picture.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис змінено.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // редагування запису

				} // збереження форми в базі
			},
			'80%', 420); // modalwindow

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
				formAdmin.picture.value = response[0].picture;
				let imgBook = document.querySelector('form.admin #img-book');
                if (response[0].picture) {imgBook.setAttribute('src', '<?=NEWS_PHOTO_FOLDER?>' + response[0].picture)}
                else imgBook.style.display = 'none';
			}, '<?=PHP_PATH?>')
		}
// ---------------------------------------------------------------------------------------------------------------------
let formAdmin = document.forms.addEdit;

document.querySelector('#picture-choice').addEventListener('click', (event)=>{ // вибір зображення
    document.querySelector('#picture-list').style.display = 'flex';
})

document.querySelector('#picture-list').addEventListener('mouseleave', (event)=>{ // закриття вікна вибору зображень
    event.target.style.display = 'none';
})

document.querySelector('#picture-list').addEventListener('click', (event)=>{ // видалення зображення (лише тих, що не прив'язані до жодної книги)
    if (event.target.tagName == 'IMG') {
        if (event.target.dataset.id == 'del') {
            if (confirm(`Видалити ${event.target.dataset.file} ?`)) {
                queryDelFile(`<?=AUTHOR_PHOTO_FOLDER?>${event.target.dataset.file}`, (response)=>{
                    if (response.error == 0) {alert ('Файл видалено.')} else alert('Помилка! Файл не видалено.');
                }, '<?=PHP_PATH?>')
            }
            return;
        }
        let l = '<?=NEWS_PHOTO_FOLDER?>'.length;
        let s = event.target.getAttribute('src');
        formAdmin.picture.value = s.substr(l, s.length);
        event.currentTarget.style.display = 'none';
        }
})

document.querySelector('#picture-upload').addEventListener('change', (event)=>{ // завантаження зображення
    document.querySelector('.wait').style.visibility = 'visible';
    setTimeout(()=>{
    document.querySelector('.wait').style.visibility = 'hidden';
    upLoad(event.target.files[0], 'assets/img/news/', (response)=>{
        if (response.error == 0 && response.upload) {
            formAdmin.picture.value = response.filename;
            alert(`Файл ${response.filename} завантажено.`);
        }
        if (response.error == 1) alert("Перевищено розмір файлу 200Mb.")
        if (response.error == 2) alert("Невірний формат файлу.")
        console.log(response.upload)
    }, '<?=PHP_PATH?>', 'image', 209715200)
}, 1000);
})

document.querySelector('#picture-clear').addEventListener('click', (event)=>{ // очистка зображення
    formAdmin.picture.value = '';
})

// -------------------------------
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