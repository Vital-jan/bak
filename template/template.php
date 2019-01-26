<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Видавництво БАК</title>
	<link rel="stylesheet" href="/bak/assets/styles.css">
	<link rel="shortcut icon" href="/bak/assets/img/favicon.ico" type="image/x-icon">
</head>

<?
	$nav_array = array( // Головне меню
		array(
		"name"=>"Головна", 
		"path"=>"/bak",
		"file"=>"main_view"),
		array (
			"name"=>"Про нас",
			"path"=>"/bak/about",
			"file"=>"about_view"),
		array (
			"name"=>"Книги",
			"path"=>"/bak/books",
			"file"=>"book_view"),
		array (
			"name"=>"Автори",
			"path"=>"/bak/authors",
			"file"=>"authors_view"),
		array (
			"name"=>"Новини",
			"path"=>"/bak/news",
			"file"=>"news_view"),
		array (
			"name"=>"Контакти",
			"path"=>"/bak/contacts",
			"file"=>"contacts_view"),
		);
?>

<body class="bak-project">
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
		FOOTER
	</footer>
	
</body>
</html>