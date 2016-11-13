<?php

namespace Webservice\Model;

use Application\MccHelpers;
use Doctrine\ORM\EntityManager;

class ActionEuropeClient {


	protected $apiUrl;
	protected $identCode;
	protected $configuration;
	protected $entityManager;

	/**
	 * ActionEuropeClient constructor.
	 *
	 * @param EntityManager $entityManager
	 * @param array $configuration
	 */
	public function __construct($entityManager, $configuration) {
		set_time_limit(0);
		$this->entityManager = $entityManager;
		$this->configuration = $configuration;
		$this->identCode = $configuration['eserviceIdentCode'];
		$this->apiUrl = $configuration['eserviceApiUrl'];
	}

	/**
	 * Get Products from Action Europe.
	 *
	 * @return array
	 */
	public function getProducts() {
		$link = $this->getGeneratedUrl('eserviceKdnr', 'eserviceGuid', '&type=si&ver=2');
		$handler = fopen($link,'r');
		$products = MccHelpers::csvStreamToArray($handler,'|');
		foreach ($products as $product) {
			$productsStocksAndPrice[] = array(
				'ProductId' => $product['Artikel-Nummer'],
				'Price'     => $product['Netto-Preis in EUR'],
				'Quantity'  => $product['Bestand'],
			);
		}
		return $productsStocksAndPrice;
	}

	/**
	 * Post an order by xml to ActionEurope
	 *
	 * Return array format
	 *          array['code'] http code of the response
	 *          array['content'] html content of the response
	 * @param string $xmlData
	 * @return array
	 */
	public function postOrder($xmlData)
	{
		$url = $this->buildRequestUrl('orderRequest');
		$output = $this->sendRequest($url, $xmlData);
		return $output;
	}



	/**
	 * Generowanie URL dla Action Europe.
	 * Funkcja przyjmuje parametry (string) i sprawdza w configuration czy istnieje taki klucz jak parametr
	 * Jeżeli istnieje to dodaje klucz (z usuniętym 'eservice') oraz jego wartość do URL (jako GET)
	 * np. parametr 'eserviceGuid' -> URL+'?guid=wartoscConfig'
	 * Jeżeli parametr nie istnieje w configuration wtedy dodaje go bezpośrednio do URL
	 *
	 * @return string
	 */
	public function getGeneratedUrl() {
		$link = $this->configuration['eserviceUrl'];
		foreach (func_get_args() as $param) {
			if(isset($this->configuration[$param])) {
				$getKey = strtolower(str_replace('eservice','', $param));
				$separator = ($link == $this->configuration['eserviceUrl']) ? '?' : '&';
				$link .= $separator.$getKey.'='.$this->configuration[$param];
			}
			else {
				$link .= $param;
			}
		}
		return $link;
	}

	/**
	 * Builds url for a specified request
	 * @param string $documentType
	 * @return string
	 */
	private function buildRequestUrl($documentType)
	{
		return $this->apiUrl . '?' . \http_build_query(['identCode' => $this->identCode, 'documentType' => $documentType]);
	}

	/**
	 * Sends a request with a specified xml data
	 *
	 *
	 * Return array format
	 *          array['code'] http code of the response
	 *          array['content'] html content of the response
	 * @param string $url The url for the request to be sent to
	 * @param string $xmlData Plain xml string to be sent
	 * @return array the response
	 */
	private function sendRequest($url, $xmlData)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Content-Length: ".strlen($xmlData)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return [
			'code'   => $info['http_code'],
			'output' => $output,
		];
	}
}
