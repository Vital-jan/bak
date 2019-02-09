<!DOCTYPE html>

<?
	const ROOT = "/bak";

	require $_SERVER['DOCUMENT_ROOT'].ROOT."/php_ajax/connect.php";
	$query = getQuery('admin','');
	$content = mysql_fetch_assoc($query);
	$nav_array = array( // Головне меню
		array(
		"name"=>"Головна",
		"path"=>ROOT,
		"file"=>"main_view"),
		array (
			"name"=>"Про нас",
			"path"=>ROOT."/about",
			"file"=>"about_view"),
		array (
			"name"=>"Книги",
			"path"=>ROOT."/books",
			"file"=>"book_view"),
		array (
			"name"=>"Автори",
			"path"=>ROOT."/authors",
			"file"=>"authors_view"),
		array (
			"name"=>"Новини",
			"path"=>ROOT."/news",
			"file"=>"news_view"),
		array (
			"name"=>"Контакти",
			"path"=>ROOT."/contacts",
			"file"=>"contacts_view"),
		);
?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?
	require "{$view_file_title}.php"
	?>
	<link rel="stylesheet" href="<?=ROOT?>/assets/styles.css">
	<link rel="shortcut icon" href="<?=ROOT?>/assets/img/favicon.ico" type="image/x-icon">
</head>

<body class="bak-project">
	<button id="login-button" type=button>Авторизуватись</button>

	<header>
		<div class="logo">
		<div class="logo-gradient"></div>
		</div>
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
	let btnLogin = document.querySelector('#login-button')
	btnLogin.addEventListener('click', (event)=>{
		if (btnLogin.style.opacity != 1) return;
		alert('Authorization');
	});
	btnLogin.addEventListener('mouseenter', (event)=>{
		interval = setTimeout(() => {
			btnLogin.style.opacity=1;
		}, 3000);
	});
	btnLogin.addEventListener('mouseout', (event)=>{
		btnLogin.style.opacity=0;
	});
</script>
</html>