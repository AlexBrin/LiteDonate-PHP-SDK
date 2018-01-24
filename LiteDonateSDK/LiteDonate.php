<?php

/**
 * © LiteDonate, 2017
 * 
 * @author AlexBrin
 */

namespace LiteDonateSDK;

class LiteDonate {
	/**
	 * @constant(API_DOMAIN) [Адрес API-сервера]
	 */
	const API_DOMAIN = 'http://api.autodonate.su/';

	/**
	 * ID магазина
	 * @var int
	 */
	private $shopId;
	/**
	 * Массив с информацией о магазине
	 * @var array
	 */
	private $info;

	/**
	 * Массив с данными о последнем запросе
	 * @var array
	 */
	private $lastRequest = [];

	/**
	 * Массив товаров
	 * @var array
	 */
	protected $products;

	/**
	 * Последние покупки
	 * @var array
	 */
	protected $lastPurchases;

	/**
	 * Способ получения данных из формы
	 * Если true - данные получаются из $_POST
	 * Иначе из $_GET
	 * @var bool
	 */
	private static $post;

	/**
	 * Экземпляр класса
	 * @var LiteDonate
	 */
	private static $instance;

	/**
	 * constructor 
	 * 
	 * @param string $token
	 * @param bool $post
	 */
	public function __construct($shopId, $post = true) {
		$this->shopId = $shopId;
		self::$post = $post;
		self::$instance = &$this;
		$this->createView();
	}

	/**
	 * Отправляет запрос на сервер API
	 * 
	 * @param  string $method 
	 * @param  array  $data   
	 * @return array          [Возвращает массив с ответом]
	 */
	public function request($method, $data = []) {
		$url = $this->prepare($method, $data);
		$response = file_get_contents($url);
		$response = json_decode($response, true);

		$this->lastRequest = [
			'url' => $url,
			'response' => $response
		];

		return $response;
	}

	/**
	 * Если все прошло хорошо - возвращает url. Иначе null
	 * 
	 * @return string|null
	 */
	public function createPay() {
		$data = self::$post ? $_POST : $_GET;
		$request = $this->request('shop/pay', $data);
		if($request['status'] === 'success')
			return $request['response']['redirectUrl'];

		return null;
	}

	public function getInfo() {
		if(!$this->info)
			$this->info = $this->request('shop')['response'];
		return $this->info;
	}

	public function getProducts() {
		if(!$this->products)
			$this->products = $this->request('shop/products')['response'];
		return $this->products;
	}

	/**
	 * Возвращает последние покупки
	 * @return array 
	 */
	public function getLastPurchases($count = 5) {
		if(!$this->lastPurchases)
			$this->lastPurchases = $this->request('shop/last', ['count' = $count])['response'];
		return $this->lastPurchases;
	}

	/**
	 * Возвращает данные последнего запроса: url и массив с ответом
	 * 
	 * @return array
	 */
	public function getLastRequest() {
		return $this->lastRequest;
	}

	/**
	 * Создает новый просмотр на платформе
	 * Используется для статистики
	 */
	private function createView() {
		$this->request('shop/view');
	}

	/**
	 * @param  string $method
	 * @param  array $data
	 * @return string
	 */
	private function prepare($method, $data) {
		$data['shopId'] = $this->shopId;
		$data = http_build_query($data);

		return self::API_DOMAIN.$method.'?'.$data;
	}

	public static function getInstance() {
		return self::$instance;
	}

}

?>