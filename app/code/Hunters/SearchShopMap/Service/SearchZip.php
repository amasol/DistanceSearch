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

    public function total($company)
    {
        $result = array();
        if ($this->validateZipCode($company['postcode'])) {
            $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        $params = [
            'query' => [
                'components' => 'postal_code:'.$company['postcode'],
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
                $result['postcode'] = $company['postcode'];
                $result['state'] = $responseDataArray['results'][0]['address_components'][2]['long_name'];
                $result['coordinate'] = json_encode($responseDataArray['results'][0]['geometry']['location']);
                $result['company_name'] = $company['company_name'];
                $result['company_email'] = $company['company_email'];
                $result['telephone'] = $company['telephone'];
                $result['city'] = $company['city'];
                $result['street'] = $company['street'];
                return json_encode($result);
            }
        } else
            {
            return NULL;
        }
    }
}