<?
session_start();
	const PHPFOLDER = "/bak/php_ajax/";
	require $_SERVER['DOCUMENT_ROOT'].PHPFOLDER."connect.php";
	$nav_array = array( // Головне меню
		array(
		"name"=>"Головна",
		"path"=>ROOTFOLDER,
		"file"=>"main_view"),
		array (
			"name"=>"Про нас",
			"path"=>ROOTFOLDER."about",
			"file"=>"about_view"),
		array (
			"name"=>"Книги",
			"path"=>ROOTFOLDER."books",
			"file"=>"book_view"),
		array (
			"name"=>"Автори",
			"path"=>ROOTFOLDER."authors",
			"file"=>"authors_view"),
		array (
			"name"=>"Новини",
			"path"=>ROOTFOLDER."news",
			"file"=>"news_view"),
		array (
			"name"=>"Контакти",
			"path"=>ROOTFOLDER."contacts",
			"file"=>"contacts_view"),
		);
?>

<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<?
	require "{$view_file_title}.php"
	?>
	<link rel="stylesheet" href="<?=ROOTFOLDER?>assets/styles.css">
	<link rel="stylesheet" href="<?=ROOTFOLDER?>assets/explorer.css">
	<link rel="shortcut icon" href="<?=ROOTFOLDER?>assets/img/favicon.ico" type="image/x-icon">
	<script src="<?=ROOTFOLDER?>assets/js/explorer.js"></script>
	<script src="<?=PHP_PATH?>/mysqlajax.js"></script>
</head>

<body class="bak-project">
	<a href="<?=ROOTFOLDER?>admin">
	<button id="login-button" type=button style="z-index: 1000">Авторизуватись</button>
	</a>

	<header>
		<div class="logo">
		<div class="logo-gradient"></div>
		</div>

		<img id="nav-button" src="<?=PATH?>assets/img/menu.png" alt=""><!-- кнопка навігація смартфон-->
		<ul id="smart-nav"><!-- меню навігація смартфон-->
		<?
		foreach($nav_array as $key=>$value) {
			$attr = ($view_file == $value['file']) ? 'class="nav-active"' : '';
			echo "
				<a href='{$value['path']}' {$attr}>
					<li>{$value['name']}</li>
				</a>";
		}
		?>
		</ul>

	<nav>
		<?
		foreach($nav_array as $key=>$value) {
			$attr = ($view_file == $value['file']) ? 'class="nav-active"' : '';
			echo "<a href='{$value['path']}' {$attr}>{$value['name']}</a>";
		}
		?>
	</nav>
	</header>
	
	<main>
		<?
			include $view_file.".php";
		?>
	</main>
	<footer>
		<?=$content['footer']?>
	</footer>
	
</body>
<script>
	document.addEventListener("DOMContentLoaded", ()=>{
	let interval;
	
	let btnLogin = document.querySelector('#login-button')
	btnLogin.addEventListener('click', (event)=>{
		if (btnLogin.style.opacity != 1) return;
	});

	btnLogin.addEventListener('mouseenter', (event)=>{
		interval = setTimeout(() => {
			btnLogin.style.opacity=1;
		}, 3000);
	});
	btnLogin.addEventListener('mouseout', (event)=>{
		btnLogin.style.opacity=0;
		clearTimeout(interval);
	});

	document.body.addEventListener('click', (event)=>{
		let btn = document.querySelector('#nav-button');
		let nav = document.querySelector('#smart-nav');
		if (event.target != btn && event.target != nav) {
			nav.style.display = 'none';
			return;
		}
		nav.style.display = nav.style.display == 'block' ? 'none' : 'block';
		if (nav.style.display == 'block') fade(nav, 300);
	})
	}) // onload
</script>

</html>