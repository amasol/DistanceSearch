<?php


namespace Hunters\MultiFeed\Helper\Feeds;

use Hunters\MultiFeed\Helper\FeedInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Hunters\MultiFeed\Helper\ContactConfiguration;

class Contact extends ContactConfiguration implements FeedInterface
{
    public function generate($filterIds, $storeId = null)
    {
        $data = [];
        $data[] = $this->getHeaders();
        sort($filterIds);
        foreach ($filterIds as $id) {
            try {
                $customer = $this->customerRepository->getById($id);
                $shippingAddressId = $customer->getDefaultShipping();
                $billingAddressId = $customer->getDefaultBilling();
                $shippingAddress = $this->addressRepository->getById($shippingAddressId);
                $billingAddress = $this->addressRepository->getById($billingAddressId);
                $shippingRegion = $this->region->load($shippingAddress->getRegionId())->getName();
                $billingRegion = $this->region->load($billingAddress->getRegionId())->getName();
            } catch (\Exception $e) {
                continue ;
            }
            $data[] = [
                $this->getCustomerEmail($customer),
                $this->getCustomerFirstName($customer),
                $this->getCustomerLastName($customer),
                $shippingAddress->getTelephone(),
		$this->getAccountIdByCustomerId($id),
		"",
                $id,
                $shippingAddress->getStreet()[0],
                $shippingAddress->getCity(),
                $shippingRegion,
                $shippingAddress->getPostCode(),
                $shippingAddress->getCountryId(),
                $billingAddress->getStreet()[0],
                $billingAddress->getCity(),
                $billingRegion,
                $billingAddress->getPostCode(),
                $billingAddress->getCountryId()
            ];

        }
        return $this->csv->generate('multifeed_contact', $data, $storeId);
    }
}
