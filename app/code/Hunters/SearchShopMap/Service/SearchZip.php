<?php

namespace Hunters\SearchShopMap\Service;

use Magento\Framework\App\Action\Action;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;

class SearchZip
{
	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $connection;

	const API_REQUEST_URI = 'https://maps.googleapis.com/';
	private $clientFactory;

	public function __construct (
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


    public function total($zip)
    {
        $result = array();
        if ($this->validateZipCode($zip))
        {

        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        $params = [
            'query' => [
                'components' => 'postal_code:'.$zip,
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
            if ($responseDataArray['results'])
            {
                $result['postcode'] = $zip;
                $result['state'] = $responseDataArray['results'][0]['address_components'][2]['long_name'];
                $result['coordinate'] = json_encode($responseDataArray['results'][0]['geometry']['location']);
                return json_encode($result);
            }
        } else
            {
            return NULL;
        }
    }
}