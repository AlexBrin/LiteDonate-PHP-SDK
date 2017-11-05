<?php

/**
 * © LiteDonate, 2017
 * 
 * @author AlexBrin
 */

declare(strict_types=1);

namespace LiteDonateSDK;

class LiteDonate {
	/**
	 * @constant(API_DOMAIN) [Адрес API-сервера]
	 */
	const API_DOMAIN = 'http://api.autodonate.su/';

	/**
	 * Ключ доступа магазина
	 * @var string
	 */
	private $token;
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
	 */
	public function __construct(string $token, bool $post = true) {
		$this->token = $token;
		self::$post = $post;
		self::$instance = &$this;
	}

	/**
	 * Отправляет запрос на сервер API
	 * 
	 * @param  string $method 
	 * @param  array  $data   
	 * @return array          [Возвращает массив с ответом]
	 */
	public function request(string $method, array $data = []): array {
		$url = $this->prepare($method, $data);
		$response = file_get_contents($url);
		$response = json_decode($response, true);

		$this->lastRequest = [
			'url' => $url,
			'response' => $response
		];

		return $response;
	}

	public function createPay(): string {
		$data = self::$post ? $_POST : $_GET;
		$request = $this->request('shop/pay', $data);
		if($request['status'] === 'success')
			return $request['response']['redirectUrl'];

		return '';
	}

	public function getInfo(): array {
		if(!$this->info)
			$this->info = $this->request('shop')['response'];
		return $this->info;
	}

	public function getProducts(): array {
		if(!$this->products)
			$this->products = $this->request('shop/products')['response'];
		return $this->products;
	}

	/**
	 * Возвращает данные последнего запроса: url и массив с ответом
	 * 
	 * @return array
	 */
	public function getLastRequest(): array {
		return $this->lastRequest;
	}

	/**
	 * Возвращает токен магазина
	 * 
	 * @return string 
	 */
	public function getToken(): string {
		return $this->token;
	}

	/**
	 * Создает новый просмотр на платформе
	 * Используется для статистики
	 */
	private function createView(): void {
		$this->request('shop/view');
	}

	private function prepare(string $method, array $data): string {
		$data['token'] = $this->token;
		$data = http_build_query($data);

		return self::API_DOMAIN.$method.'?'.$data;
	}

	public static function getInstance(): LiteDonate {
		return self::$instance;
	}

}

?>