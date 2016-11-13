<?php

namespace Webservice\Model;

class ActionClient extends \SoapClient {

	protected $wsdl;
	protected $cdn;
	protected $customerId;
	protected $userName;
	protected $userPassword;
	protected $passwordHash;
	protected $entityManager;
    protected $retryCounter;
    protected $maxRetry = 3;


	/**
	 * ActionClient constructor.
	 *
	 * @param mixed $entityManager
	 * @param array $configuration
	 */
	public function __construct($entityManager, $configuration) {
		set_time_limit(0);
		$this->customerId = $configuration['customerId'];
		$this->userName = $configuration['userName'];
		$this->userPassword = $configuration['userPassword'];
		$this->passwordHash = $configuration['passwordHash'];
		$this->wsdl = $configuration['wsdl'];
		$this->cdn = $configuration['cdn'];
        $this->retryCounter = 0;
		$this->entityManager = $entityManager;
		parent::__construct($this->wsdl, ['keep_alive' => false, 'connection_timeout' => 700]);
	}

	/**
	 * Get call parameters including authorization credentials.
	 *
	 * @param array $parameters
	 *
	 * @return array
	 */
	protected function getCallParameters($parameters = []) {
		$credentials = [
				'customerId'   => $this->customerId,
				'userName'     => $this->userName,
				'userPassword' => $this->userPassword,
		];
		return array_merge_recursive($credentials, $parameters);
	}

	/**
	 * Get Products.
	 *
	 * @return array|null
	 */
	public function getProducts() {
	    try {
            $result = $this->Products_Get($this->getCallParameters());
        } catch (\Exception $exception) {
            return $this->retryOperation($exception, 'getProducts');
        }
		if ($result->Products_GetResult->Result === true && isset($result->Products_GetResult->Products)) {
			return $result->Products_GetResult->Products->DEProduct;
		}
		return null;
	}

	/**
	 * Get Product Eans.
	 *
	 * @return array|null
	 */
	public function getProductEans() {
	    try {
            $result = $this->Products_EAN_Get($this->getCallParameters());
        } catch (\Exception $exception) {
            return $this->retryOperation($exception, 'getProductEans');
        }
		if ($result->Products_EAN_GetResult->Result === true && isset($result->Products_EAN_GetResult->ProductEANs)) {
			$productEans = [];
			foreach($result->Products_EAN_GetResult->ProductEANs->DEProductEAN as $productEAN) {
				$productEans[$productEAN->ProductId][] = $productEAN->EANCode;
			}
			return $productEans;
		}
		return null;
	}

	/**
	 * Get Product Photos.
	 *
	 * @param string $productId
	 *
	 * @return array|null
	 */
	public function getProductPhotos($productId) {
	    try {
            $result = $this->Product_Pictures_Get($this->getCallParameters([
                'productId' => $productId
            ]));
        } catch (\Exception $exception) {
            return $this->retryOperation($exception, 'getProductPhotos', $productId);
        }
		if ($result->Product_Pictures_GetResult->Result === true && isset($result->Product_Pictures_GetResult->Pictures)) {
			$data = $result->Product_Pictures_GetResult->Pictures->DEPictures;
			if (!is_array($data)) {
				$data = (empty($data)) ? [] : [$data];
			}
			return $data;
		}
		return null;
	}

	/**
	 * Get Exchange Rate.
	 *
	 * @param string $currency
	 *
	 * @return float|null
	 */
	public function getExchangeRate($currency = 'EUR') {
        try {
            $result = $this->ExchangeRates_Get($this->getCallParameters([
                'currency' => $currency,
            ]));
        } catch (\Exception $exception) {
            return $this->retryOperation($exception, 'getExchangeRate', $currency);
        }
		if ($result->ExchangeRates_GetResult->Result === true && isset($result->ExchangeRates_GetResult->ExchangeRate)) {
			return floatval($result->ExchangeRates_GetResult->ExchangeRate->ExchnageRate);
		}
		return null;
	}

	/**
	 * Get Photo Url for CDN.
	 *
	 * @return string
	 */
	public function getPhotoUrl() {
		return $this->cdn . 'File.aspx?CID=' . $this->customerId . '&UID=' . $this->userName . '&PID=' . $this->passwordHash . '&P=';
	}


    /**
     * Retry operation by the client.
     *
     * @param \Exception $exception
     * @param string $method
     * @param null|mixed $argument
     * @return mixed
     * @throws \Exception
     */
    protected function retryOperation(\Exception $exception, $method, $argument = null) {
        if (
            $exception->getMessage() === 'Error Fetching http headers'
            && $this->maxRetry > $this->retryCounter
        ) {
            $this->retryCounter++;
            sleep(5);
            return $this->$method($argument);
        } else {
            throw $exception;
        }
    }
}
