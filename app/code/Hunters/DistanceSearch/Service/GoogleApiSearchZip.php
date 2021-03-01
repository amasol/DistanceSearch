<?php

namespace Hunters\DistanceSearch\Service;

use Magento\Framework\App\Action\Action;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;

class GoogleApiSearchZip
{
	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $connection;

	//Passkey=&Include=Answers&Filter=ProductId:FIBERS&Limit=10&Offset=10
	const API_REQUEST_URI = 'https://maps.googleapis.com/';
	private $clientFactory;

	public function __construct(
		\Magento\Framework\App\ResourceConnection $connection,
		ClientFactory $clientFactory
	)
	{
		$this->clientFactory = $clientFactory;
		$this->connection = $connection;
	}

//      должна быть валидацыя данных
    public function total2($zip){

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
        $total = $responseDataArray['results'][0]['geometry']['location'];
        return json_encode($total);
    }
}