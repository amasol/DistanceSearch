<?php


namespace Hunters\MultiFeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Hunters\MultiFeed\Helper\Csv;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Directory\Model\Region;
use Magento\Framework\App\ResourceConnection;


class ContactConfiguration extends AbstractHelper
{

    protected $csv;
    protected $customerRepository;
    protected $addressRepository;
    protected $region;
    protected $_resourceConnection;


    public function __construct(
        Csv $csv,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        Region $region,
	ResourceConnection $resourceConnection
    )
    {
        $this->csv = $csv;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->region = $region;
        $this->_resourceConnection = $resourceConnection;
    }

    public function getAccountIdByCustomerId($customerId) {
        $sql = "select company_id from company_advanced_customer_entity where customer_id = :customerId and company_id != 0 limit 1;";
        $bind = [
            'customerId'    =>  $customerId
        ];
        return $this->_resourceConnection
            ->getConnection()
            ->fetchOne($sql, $bind);
    }

    public function getCustomerEmail($customer)
    {
        return $customer->getEmail();
    }

    public function getCustomerFirstName($customer)
    {
        return $customer->getFirstName();
    }

    public function getCustomerLastName($customer)
    {
        return $customer->getLastName();
    }


    public function getHeaders()
    {
        return [
            "Email",
            "First Name",
            "Last Name",
            "PhoneNumber",
            "Account ID",
            "Primary Contact",
            "Customer ID",
            "Shipping Street",
            "Shipping City",
            "Shipping State",
            "Shipping Postal Code",
            "Shipping Country",
            "Billing Street",
            "Billing City",
            "Billing State",
            "Billing Postal Code",
            "Billing Country"
        ];
    }

}
