<?php

namespace Hunters\SearchShopMap\Service;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;

class SearchZip
{
    const API_REQUEST_URI = 'https://maps.googleapis.com/';

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $connection;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

	public function __construct(
		\Magento\Framework\App\ResourceConnection $connection,
		ClientFactory $clientFactory
	) {
		$this->clientFactory = $clientFactory;
		$this->connection = $connection;
	}

	public function validateZipCode($zipCode)
    {
        if (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $zipCode)) {
            return true;
        } else {
            return false;
        }
	}

    public function total($coordinate)
    {
        $result = array();
        if ($this->validateZipCode($coordinate['postcode'])) {
            $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);
        $params = [
            'query' => [
                'address' => $coordinate['city'].'+'.$coordinate['street'],
                'key' => 'AIzaSyBSiWyPtBS2Esy_ObhOSQJT81AfU3jyXcQ'
            ]
        ];
        $response = null;
        try {
            $response = $client->request(
                'GET',
                'maps/api/geocode/json?',
                $params
            );
        } catch (GuzzleException $exception) {
            return $exception->getMessage();
        }

        $responseDataArray = json_decode($response->getBody()->getContents(), true);
            if (isset($responseDataArray['results']) && isset($coordinate['company_id'])) {
                $result['postcode'] = $coordinate['postcode'];
                $result['coordinate'] = json_encode($responseDataArray['results'][0]['geometry']['location']);
                $result['company_id'] = $coordinate['company_id'];
                $result['company_name'] = $coordinate['company_name'];
                $result['company_email'] = $coordinate['company_email'];
                $result['telephone'] = $coordinate['telephone'];
                $result['city'] = $coordinate['city'];
                $result['street'] = $coordinate['street'];
                return json_encode($result);
            }
        } else {
            return NULL;
        }
    }


//      эта функция испльзуется для получение данных в начем ajax контроллере ....
    public function totalTwo($zip)
    {
        $result = array();
        if ($this->validateZipCode($zip)) {
            $client = $this->clientFactory->create(['config' => [
                'base_uri' => self::API_REQUEST_URI
            ]]);

            $params = [
                'query' => [
                    'components' => 'postal_code:' . $zip,
                    'key' => 'AIzaSyBSiWyPtBS2Esy_ObhOSQJT81AfU3jyXcQ'
                ]
            ];
            $response = null;
            try {
                $response = $client->request(
                    'GET',
                    'maps/api/geocode/json?',
                    $params
                );
            } catch (GuzzleException $exception) {
                return $exception->getMessage();
            }

            $responseDataArray = json_decode($response->getBody()->getContents(), true);
            if ($responseDataArray['results']) {
                $result['postcode'] = $zip;
                $result['coordinate'] = json_encode($responseDataArray['results'][0]['geometry']['location']);
                return json_encode($result);
            }
        } else {
            return NULL;
        }
    }
}