LiteDonate PHP SDK
==================

PHP SDK для [LiteDonate](http://autodonate.su)

Пример
------

Это просто пример из [example.php](/example.php)

```
<?php

	require_once 'LiteDonate/LiteDonate.php';
	use LiteDonate\LiteDonate;

	// Создаем экземпляр класса 
	$ld = new LiteDonate('API ключ', 0 /*ID магазина*/);

	// Если это покупка
	if(isset($_POST['buy'])) {
		// Выполняем действие покупки
		// В ответ придет массив -- ответ сервера
		$result = $ld->buyProduct();
		// Если создание платежа прошло успешно, то
		if($result['status'] == 'success') {
			// Перенаправляем пользователя на адрес, который нам вернулся
			header('Location: ' . $result['response']['redirect']);
			// Так же там содержится сообщение о необходимости перенаправления
		}
		// Иначе говорим об ошибке
		else {
			echo '<pre>';
			// Информация о последнем запросе
			// Дополнительно приложен URL отправляемого запроса
			print_r($ld->getLastRequest());
			// Или
			// просто ответ сервера
			print_r($result);
			echo '</pre>';
			die;
		}
	}

	// Получаем товары магазина
	$products = $ld->getProducts();
?>

<form action="" method="post">
									<!-- Обязательно заполнять так -->
	<input type="text" name="LiteDonate[nickname]" placeholder="Логин"> <br>
					<!-- Обязательно заполнять так -->
	<select name="LiteDonate[product]" id="">
		<option value="0" selected disabled>Выберите товар</option>
		<?php foreach($products as $product): /* Перебор и вывод товаров */ ?>
			<option value="<?=$product['id']?>"><?=$product['name']?> (<?=$product['price']?>р.)</option>
		<?php endforeach; ?>
	</select> <br>
	<input name="buy" type="submit" value="Купить / Доплатить">
</form>
```