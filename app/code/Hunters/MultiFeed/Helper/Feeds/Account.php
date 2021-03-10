<?php


namespace Hunters\MultiFeed\Helper\Feeds;

use Hunters\MultiFeed\Helper\FeedInterface;
use Hunters\MultiFeed\Helper\AccountConfiguration;

class Account extends AccountConfiguration implements FeedInterface
{
    public function generate($filterIds, $storeId = null)
    {
        $data = [];
        $data[] = $this->getHeaders();
        sort($filterIds);
        foreach ($filterIds as $id) {
            $company    =   $this->_account->load($id);
            $customer   =   $this->_customer->load($this->getSuperuserId($company));
            $adminUser  =   $this->_adminUser->load($this->getSalesRepresentativeId($company));
            $shippingRegion = $this->region->load($company->getData('region_id'))->getName();

            $data[] = [
                $this->getCompanyName($company),
                $this->getCompanyEmail($company),
                '',
                '',
                $this->getCompanyPhoneNumber($company),
                'Professional',
                '',
                '',
                strval($this->getCompanyShippingStreet($company)),
                strval($this->getCompanyShippingCity($company)),
                strval($shippingRegion),
                strval($this->getCompanyShippingPostCode($company)),
                strval($this->getCompanyShippingCountry($company)),
                strval($this->getCompanyShippingStreet($company)),
                strval($this->getCompanyShippingCity($company)),
                strval($shippingRegion),
                strval($this->getCompanyShippingPostCode($company)),
                strval($this->getCompanyShippingCountry($company)),
                $this->getSalesforceUniqueIdentifier($customer),
                $this->getCompanyId($company),
                $this->getSalesRep($adminUser)
            ];
        }
        return $this->csv->generate('multifeed_account', $data, $storeId);
    }
}
