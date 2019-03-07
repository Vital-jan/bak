<?
$query = mysql_query('SELECT admin.company, admin.address, admin.phone, admin.email, admin.fb, admin.fb_view FROM admin');
$contacts = mysql_fetch_assoc($query);
$query = getQuery('shops');
$shops = array();
while ($cRecord = mysql_fetch_assoc($query)) {
	$shops[] = $cRecord;
}
$login = getLogin();

if ($login) {
	echo "<button type='button' id='edit'> Редагувати контакти </button><br>"; 
};

?>
<div class="main-content">
	<address>
	<ul>
		<li><?=$contacts['company']?></li>
		<li><?=$contacts['address']?></li>
		<li><img class="phone" src="../assets/img/phone.png" alt=""><?=$contacts['phone']?></li>
		<li><img class="phone" src="../assets/img/email.png" alt="" class="phone"><?=$contacts['email']?></li>
		<?if ($contacts['fb_view']):?>
			<li><a href="<?=$contacts['fb']?>" target="blank"><img  class="phone"src="../assets/img/fb.png" alt="" class="phone">	Ми у facebook</a></li>
		<?endif;?>
		<li></li>
		<li class="separate"></li>
		<li></li>
	</ul>
	</address>

	<h4 class="vert-align click" id="where-buy" >
		<img style="height: 2.5em" src="../assets/img/cart.png" alt="">
		Де придбати книжки ?
	</h2>

	<div id="shop-list">
<?
if ($login) {
	echo "<button id='shop-add' type='button'> Додати книгарню </button><br>"; 
};
?>

		<h2>Інтернет книгарні:</h2>
		<ul>
			<?

				foreach($shops as $key=>$value) {
					if ($login) {$btns = "<img id='shop-edit' data-id='{$value['shop_id']}' class='edit-button' src='../assets/img/edit-button.png'><img id='shop-delete' data-id='{$value['shop_id']}' class='edit-button' src='../assets/img/close.png'>";} else $btns='';
					if ($value['www'] == 1) echo "
					<li class='shop-item'>
					${btns}
					<a href='http://{$value['url']}' target='blanc'>{$value['url']}</a>
					</li>";
				}
				?>
		</ul>
		<br>
		<h2>Книгарні:</h2>
		<br>
		<?
			foreach($shops as $key=>$value) {
				if ($value['www'] != 1) {
					if ($login) {$btns = "<img id='shop-edit' data-id='{$value['shop_id']}' class='edit-button' src='../assets/img/edit-button.png'><img id='shop-delete' data-id='{$value['shop_id']}' class='edit-button' src='../assets/img/close.png'>";} else $btns='';
					echo "<h3 class='shop-title'> {$value['title']}</h3>".
					$btns.
					"<p class='shop-item'>{$value['content']}</p>";
					
				}
				}
		?>
	</div>
	</div>
<script>
document.addEventListener("DOMContentLoaded", ()=>{
	fade(document.querySelector('.main-content'), 300);

    document.querySelector('#edit').addEventListener('click', (event)=>{ // кнопка редагування контактів
        modalWindow('Контактна інформація. Редагування.',
		`<form name="admin" class="admin">
			<ul>
			<li><span>Назва компанії:</span><input type="input" name = "company" value="<?=$contacts['company']?>"></li>
			<li><span>Адреса:</span><input type="input" name = "address" value="<?=$contacts['address']?>"></li>
			<li><span>Телефон:</span><input type="input" name = "phone" value="<?=$contacts['phone']?>"></li>
			<li><span>Email:</span><input type="input" name = "email" value="<?=$contacts['email']?>"></li>
			<li><span>FB група/сторінка:</span><input type="input" name = "fb" value="<?=$contacts['fb']?>"></li>
			<li><span>Показати FB:</span><input type="checkbox" name = "fb_view" <?if ($contacts['fb_view']) echo "checked"?> value="<?$contacts['fb_view']?>"></li>
			</ul>
		</form>`,
        ['+Зберегти', '-Скасувати'],
        (n)=>{
			if (n != 0) return;
			// збереження контенту в базі
			let f = document.forms.admin;
            queryUpdate('admin', `admin_id=1`,
            [
            ['company', f.company.value],
            ['address', f.address.value],
            ['phone', f.phone.value],
            ['email', f.email.value],
            ['fb', f.fb.value],
            ['fb_view', +f.fb_view.checked],
            ], 
            (response)=>{if (!response.sql) {alert('Помилка! Інформацію не збережено!')} else location.reload(true)}, '<?=PHP_PATH?>');
        },
        '80%', 300);
    });


	document.querySelector('#where-buy').addEventListener('click', (event)=>{
		document.querySelector('#shop-list').style.display = "block";
	});


	function addEdit (item) {
		function addEditForm(header, number = null) { // форма редагування - додавання
			modalWindow(header, `
			<form name='addEdit'  class="admin">
			<ul>
				<li><span id='wwwshop'>Інтернет-магазин:</span><input id='www' type='checkbox' name='www' checked></li>
				<li><input type='text' placeholder='http://' name='url'></li>
				<li><input class='invisible' type='text' placeholder='Заголовок' name='title'></li>
				<li><textarea class='invisible' name='content' rows='3' placeholder='Контент'></textarea></li>
			</ul>
			`
			, ['+Зберегти', '-Скасувати'], (btn)=>{
				let formAdmin = document.forms.addEdit;
				if (btn == 0) { // збереження форми в базі
					if (!item) { // додаваня запису
					queryInsert('shops', [
						['#www', +formAdmin.www.checked],
						['url', formAdmin.url.value],
						['title', formAdmin.title.value],
						['content', formAdmin.content.value]
					], (response)=>{
						if (!response.sql) {console.log(response)} else {
							alert ('Запис додано.');
							document.location.reload(true);
						};
					}, '<?=PHP_PATH?>');
				} // додаваня запису
					if (item != null) { // редагування запису
					queryUpdate('shops', `shop_id=${item}`, [
						['#www', +formAdmin.www.checked],
						['url', formAdmin.url.value],
						['title', formAdmin.title.value],
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

			let formAdmin = document.forms.addEdit;
			let www = document.querySelector('#www');
			www.addEventListener('click',()=>{
				formAdmin.url.classList.toggle('invisible');
				formAdmin.title.classList.toggle('invisible');
				formAdmin.content.classList.toggle('invisible');
			})
			
		} // addEditForm


		if (item == null) { // додавання магазину
			addEditForm ('Додати магазин');
		}
		else { // редагування магазину
			addEditForm('Редагувати магазин', item);
			let formAdmin = document.forms.addEdit;
			queryGet(`select * from shops where shop_id=${item}`, (response)=>{ // отримуємо елемент з бази
				// наповнюємо поля форми
				if (response[0].www == 0) {formAdmin.www.removeAttribute('checked')} else formAdmin.www.setAttribute('checked','');
				formAdmin.url.value = response[0].url;
				formAdmin.title.value = response[0].title;
				formAdmin.content.value = response[0].content;

				formAdmin.www.setAttribute('disabled',''); // ховаємо елементи залежно від значення поля "інтернет-магазин"
				formAdmin.www.classList.add('invisible');
				document.querySelector('#wwwshop').classList.add('invisible');
				if (response[0].www == 1) {
					formAdmin.url.classList.remove('invisible');
					formAdmin.title.classList.add('invisible');
					formAdmin.content.classList.add('invisible');
				}
				if (response[0].www == 0) {
					formAdmin.url.classList.add('invisible');
					formAdmin.title.classList.remove('invisible');
					formAdmin.content.classList.remove('invisible');
				}

			}, '<?=PHP_PATH?>')
		}
	}

	document.body.addEventListener('click', (event)=>{ // обробка кліку edit, del та add
		if (event.target.id == 'shop-add') {
			addEdit(null);
		}
		if (event.target.id == 'shop-edit') {
			addEdit(event.target.dataset.id);
			}
		if (event.target.id == 'shop-delete') { // видалення магазину
			modalWindow('Видалення елементу', 'Ви підтверджуєте видалення цього елементу?', ['Залишити', '-Видалити'], (n)=>{
				if (n == 1) {
					queryDelete('shops', `shop_id=${event.target.dataset.id}`, (response)=>{
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