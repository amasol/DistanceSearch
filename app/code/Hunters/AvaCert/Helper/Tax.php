<?php
namespace Hunters\AvaCert\Helper;

class Tax extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $apiHelper;
	protected $resource;

    public function __construct(
        Api $apiHelper,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->apiHelper = $apiHelper;
        $this->resource = $resource;
    }

	public function isFirstCompanyOrder($customerId)
	{
		$connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        
		$data = $connection->fetchAll(
			"select cace.customer_id,cace2.customer_id from company_advanced_customer_entity as cace
			left join company_advanced_customer_entity as cace2 on cace.company_id = cace2.company_id
			inner join sales_order as so on so.customer_id = cace2.customer_id
			where cace.customer_id = $customerId and cace2.company_id > 0"
		);

		if (is_array($data) && count($data)) {
			return false;
		}

		return true;
	}

	public function hasCustomerCertificate($customerId)
	{
		$json = $this->apiHelper->getCustomerCerts(
            $customerId
        );

        if (is_object($json) && property_exists($json, 'success') && ($json->success === false)){
            return false;
        }

        if (is_array($json) && isset($json[0]) && $json[0]->id){
            return true;
        }

        return false;
	}


}