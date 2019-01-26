	
	<address>
	<ul>
		<li>Видавництво "БАК"</li>
		<li>Україна, м.Львів</li>
		<li><img class="phone" src="../assets/img/phone.png" alt="">	050-1111111</li>
		<li><img src="../assets/img/email.png" alt="" class="phone">	bak@gmail.com</li>
		
		<li><hr></li>
		<li></li>
	</ul>
	</address>

	<h4 class="vert-align click" id="where-buy" >
		<img style="height: 2.5em" src="../assets/img/cart.png" alt="">
		Де придбати книжки ?
	</h2>

	<div id="shop-list">
		<h4>Інтернет книгарні:</h4>
		<ul>
			<li>Інтернет-книгарня1</li>
			<li>Інтернет-книгарня2</li>
			<li>Інтернет-книгарня3</li>
			<li>Інтернет-книгарня4</li>
			<li>Інтернет-книгарня5</li>
		</ul>
		<h4>Книгарні:</h4>
		<ul>
		<li>Книгарня 2</li>
			<li>Книгарня 3</li>
			<li>Книгарня 3</li>
			<li>Книгарня 1</li>
			<li>Книгарня 4</li>
			<li>Книгарня 5</li>
		</ul>
</div>

<script>
	document.querySelector('#where-buy').addEventListener('click', (event)=>{
		document.querySelector('#shop-list') .style.display = "block";
	});
</script>