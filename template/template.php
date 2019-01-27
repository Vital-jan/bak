<!DOCTYPE html>

<?
	const ROOT = "/bak";
	const NAME = "Видавництво \"БаК\"";
?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=NAME?></title>
	<link rel="stylesheet" href="<?=ROOT?>/assets/styles.css">
	<link rel="shortcut icon" href="<?=ROOT?>/assets/img/favicon.ico" type="image/x-icon">
</head>

<?
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