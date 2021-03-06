<?
    $query = getQuery('news', 'order by date desc');
    $news = array();
    
    while ($cRecord = mysqli_fetch_assoc($query)) {
        $news[] = $cRecord;
    }

    // $login = getLogin();

    if ($login) {
        echo "<button type='button' id='news-add'> Додати новину </button><br>"; 
    };
    foreach($news as $key=>$value) {
            $btns = '';
			if ($login) $btns = "<img class='edit-button' id='news-edit' data-id={$value['news_id']} src='../assets/img/edit-button.png'><img class='edit-button' id='news-del' data-id={$value['news_id']} src='../assets/img/close.png'>";
			$path = NEWS_PHOTO_FOLDER.$value['picture'];
            $img = ($value['picture'] != '' && $value['picture'] != null) ? "<img class='news-img' id='news-img' src='{$path}'>" : '';
            $img_large = ($value['picture'] != '' && $value['picture'] != null) ? "<img class='news-img-large' id='news-img-large' src='{$path}'>" : '';
            $value['content'] = html_entity_decode($value['content'], ENT_QUOTES);
            if (!$img) $no_picture = ' no-picture';
        echo "
			<div class='news-item'>
			<h2 class='news-header'>
            <span class='news-date'>{$value['date']}</span>
            {$btns}
            {$value['header']}
            </h2>
			<p class='news-content{$no_picture}'>
            {$value['content']}
			</p>
			{$img}
            </div>
			{$img_large}
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

let openPicture;

	document.body.addEventListener('click', (event)=>{
        if (openPicture) openPicture.style.display = 'none';
		if (event.target.id == 'news-img') {
            openPicture = event.target.parentElement.nextElementSibling;
            openPicture.style.display = 'block';
        }
	})

    let itemTimeout = 0;
    let newsList = document.querySelectorAll('.news-item');
    
    newsList.forEach((i)=>{
        setTimeout(()=>{
            fade(i, 0);
        }, itemTimeout);
        itemTimeout +=0;
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
                <li style='font-size: 0.8em'>Щоб вставити посилання, утримуйте Ctrl та клікніть лівою кнопкою в місці вставки</li>
                <li><textarea name='content' rows='5' placeholder='Контент'></textarea></li>
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
							popUpWindow ('Запис додано.', ()=>{document.location.reload(true);});
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
							popUpWindow ('Запис змінено.', ()=>{document.location.reload(true);});
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

formAdmin.content.addEventListener('click', (event)=>{// вставка посилання по ctrl + click
    if (event.ctrlKey) {
        let str = event.target.value;
        str = str.substr(0,event.target.selectionStart) + localStorage.getItem('link') + str.substr(event.target.selectionStart, str.length);
        event.target.value = str;
    }
    }) 

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
                queryDelFile(`<?=NEWS_PHOTO_FOLDER?>${event.target.dataset.file}`, (response)=>{
                    if (response.error == 0) {popUpWindow ('Файл видалено.')} else popUpWindow('Помилка! Файл не видалено.');
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
            popUpWindow(`Файл ${response.filename} завантажено.`);
        }
        if (response.error == 1) popUpWindow("Перевищено розмір файлу 2Mb.", undefined, undefined, 4)
        if (response.error == 2) popUpWindow("Невірний формат файлу.", undefined, undefined, 4)
        if (response.error == 3) popUpWindow(response.errormessage)
    }, '<?=PHP_PATH?>', 'image', 2097152)
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
							popUpWindow ('Запис видалено.', ()=>{document.location.reload(true);});
						}
					}, '<?=PHP_PATH?>');
				}
			}, '60%');
		};
	})

}) // onload
</script>