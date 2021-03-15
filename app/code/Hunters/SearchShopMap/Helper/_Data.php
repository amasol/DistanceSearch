<?php


namespace Hunters\SearchShopMap\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function getCompanyIdByCoordinate($lat, $lng)
    {
        $companyId = null;

        // you table with coordinate
//            $this->coordinate->create()->getSelect()->addFieldToFilter(/* lat == $lat and lng == $lng*/)
        return $companyId;
    }

    public function getCompanyData($entityId)
    {
        #todo return data about company

        // Magento company data Magento_Company

//            $this->company->create()->getSelect()->addFieldToFilter(/* entity_id = company_id */)
        return [];
    }
}