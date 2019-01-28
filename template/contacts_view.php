	
<!DOCTYPE html>
<?
require 'connect.php';
$query = getQuery('admin');
$contacts = mysql_fetch_assoc($query);
$query = getQuery('shops');
$shops = array();
while ($cRecord = mysql_fetch_assoc($query)) {
	$shops[] = $cRecord;
}

?>


	<address>
	<ul>
		<li><?=NAME?></li>
		<li><?=$contacts['address']?></li>
		<li><img class="phone" src="../assets/img/phone.png" alt=""><?=$contacts['phone']?></li>
		<li><img class="phone" src="../assets/img/email.png" alt="" class="phone"><?=$contacts['email']?></li>
		<li><a href="https://www.facebook.com/groups/1895460077339659/" target="blank"><img  class="phone"src="../assets/img/fb.png" alt="" class="phone">	Наша група у facebook</a></li>
		<li></li>
		<li><hr></li>
		<li></li>
	</ul>
	</address>

	<h4 class="vert-align click" id="where-buy" >
		<img style="height: 2.5em" src="../assets/img/cart.png" alt="">
		Де придбати книжки ?
	</h2>

	<div id="shop-list">
		<h2>Інтернет книгарні:</h2>
		<ul>
			<?
				foreach($shops as $key=>$value) {
					if ($value['www'] == 1) echo "<li class='shop-item'> <a href='http://{$value['url']}' target='blanc'>{$value['url']}</a></li>";
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
					"<p class='shop-item'>{$value['content']}</p>";
					
				}
				}
		?>
	</div>

<script>
	document.querySelector('#where-buy').addEventListener('click', (event)=>{
		document.querySelector('#shop-list') .style.display = "block";
	});
</script>