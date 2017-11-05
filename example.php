<?php

require_once '../LiteDonateSDK/LiteDonate.php';

use LiteDonateSDK\LiteDonate;

$sdk = new LiteDonate('ym2JMExCaNyKy-Saw3JlXSAKJ6szQQhY');

if(isset($_POST['buy'])) {
	$url = $sdk->createPay();
	if($url)
		header("Location: " . $url);
}

$shopInfo = $sdk->getInfo();

// die;
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=$shopInfo['name']?></title>
</head>
<body>
	<h1 align="center">
		<?=$shopInfo['name']?>
	</h1>
	<div class="products">
		<form action="" method="post" align="center">
			<input type="text" name="nickname" placeholder="Никнейм" required> <br>
			<input type="text" name="coupon" placeholder="Купон, если есть"> <br>
			<select name="productId" required>
				<option value="0" disabled selected>Выберите товар</option>
				<?php foreach($sdk->getProducts() as $product): ?>
					<option value="<?=$product['id']?>">
						<?=$product['name']?> (<?=$product['price']?>руб.)
					</option>
				<?php endforeach; ?>
			</select> <br>
			<input type="submit" name="buy" value="Купить">
		</form>
	</div>
	<br>
	<div class="desc">
		<h2 align="center">Описание товаров</h1>
		<?php foreach($sdk->getProducts() as $product): ?>
			<h4><?=$product['name']?></h4>
			<p><?=$product['description']?></p>
			<hr>
		<?php endforeach; ?>
	</div>
</body>
</html>