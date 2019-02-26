<?
$query = getQuery('admin');
$contacts = mysql_fetch_assoc($query);
$query = getQuery('shops');
$shops = array();
while ($cRecord = mysql_fetch_assoc($query)) {
	$shops[] = $cRecord;
}
$login = getLogin();

if ($login) {
	echo "<button type='button'> Редагувати контакти </button><br>"; 
};

?>
<div class="main-content">
	<address>
	<ul>
		<li><?=$contacts['company']?></li>
		<li><?=$contacts['address']?></li>
		<li><img class="phone" src="../assets/img/phone.png" alt=""><?=$contacts['phone']?></li>
		<li><img class="phone" src="../assets/img/email.png" alt="" class="phone"><?=$contacts['email']?></li>
		<li><a href="https://www.facebook.com/groups/1895460077339659/" target="blank"><img  class="phone"src="../assets/img/fb.png" alt="" class="phone">	Наша група у facebook</a></li>
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
	echo "<button type='button'> Додати книгарню </button><br>"; 
};
?>

		<h2>Інтернет книгарні:</h2>
		<ul>
			<?
			    if ($login) $btns = "<img class='edit-button' src='../assets/img/edit-button.png'><img class='edit-button' src='../assets/img/close.png'>";

				foreach($shops as $key=>$value) {
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
					echo "<h3 class='shop-title'> {$value['title']}</h3>".
					$btns.
					"<p class='shop-item'>{$value['content']}</p>";
					
				}
				}
		?>
	</div>
	</div>
<script src='../assets/js/explorer.js'></script>
<script>
document.addEventListener("DOMContentLoaded", ()=>{
	fade(document.querySelector('.main-content'), 300);
	document.querySelector('#where-buy').addEventListener('click', (event)=>{
		document.querySelector('#shop-list').style.display = "block";
	});

}) // onload
</script>