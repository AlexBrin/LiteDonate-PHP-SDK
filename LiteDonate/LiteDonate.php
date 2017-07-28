<?php

/**
 * 
 * @author  AlexBrin
 */

namespace LiteDonate;

class LiteDonate {
	const STATUS_SUCCESS = 'success';
	const STATUS_ERROR   = 'error';

	public static $apiDomain = 'http://api.autodonate.su/';

	private $apiToken,
					$shopId;

	public  $products;

	private $lastRequest;

	public function __construct($apiToken, $shopId) {
		$this->apiToken = $apiToken;
		$this->shopId = $shopId;
	}

	public function buyProduct() {
		$nickname = $_POST['LiteDonate']['nickname'];
		$product  = $_POST['LiteDonate']['product'];
		
		$request = $this->request('api/buy', [
			'shopId' => $this->shopId,
			'nickname' => $nickname,
			'productId' => $product,
		]);

		return $request;
	}

	public function getProducts() {
		if($this->products === null) {
			$this->products = $this->request('shop/info/' . $this->shopId, []);
			if($this->products['status'] === self::STATUS_SUCCESS)
				$this->products = $this->products['response']['products'] ? $this->products['response']['products'] : [];
		}

		return $this->products;
	}

	public function request($apiMethod, $data) {
		$url = $this->prepare($apiMethod, $data);

		$response = file_get_contents($url);

		$response = json_decode($response, true);

		$this->lastRequest = [
			'url' => $url,
			'response' => $response,
		];

		return $response;
	}

	public function getLastRequest() {
		return $this->lastRequest;
	}

	private function prepare(string $apiMethod, array $data) {
		$data['api_token'] = $this->apiToken;
		$data = http_build_query($data);

		return self::$apiDomain . $apiMethod . '?' . $data;
	}
}

?>