<?php


namespace Hunters\MultiFeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Company\Model\Company;
use Magento\Company\Model\Customer;
use Magento\User\Model\User;
use Hunters\MultiFeed\Helper\Csv;
use Magento\Directory\Model\Region;

class AccountConfiguration extends AbstractHelper
{

    protected $_account;
    protected $_customer;
    protected $csv;
    protected $_adminUser;
    protected $region;

    public function __construct(
        Company $company,
        Csv $csv,
        Customer $customer,
        User $userFactory,
        Region $region
    )
    {
        $this->csv = $csv;
        $this->_account = $company;
        $this->_customer = $customer;
        $this->_adminUser = $userFactory;
        $this->region = $region;
    }

    public function getCompanyName(Company $company)
    {
        return $company->getCompanyName();
    }

    public function getCompanyEmail(Company $company)
    {
        return $company->getCompanyEmail();
    }

    public function getCompanyPhoneNumber(Company $company)
    {
        return $company->getData('telephone');
    }

    public function getCompanyShippingStreet(Company $company)
    {
        return $company->getData('street');
    }

    public function getCompanyShippingCity(Company $company)
    {
        return $company->getCity();
    }

    public function getCompanyShippingState(Company $company)
    {
        return $company->getRegion();
    }
    public function getCompanyShippingPostCode(Company $company)
    {
        return $company->getPostCode();
    }

    public function getCompanyShippingCountry(Company $company)
    {
        return $company->getData('country_id');
    }

    public function getSuperuserId(Company $company)
    {
        return $company->getData('super_user_id');
    }
    public function getSalesRepresentativeId(Company $company)
    {
        return $company->getData('sales_representative_id');
    }

    public function getCompanyId(Company $company)
    {
        return $company->getEntityId();
    }

    public function getSalesforceUniqueIdentifier(Customer $customer)
    {
        $accountNumber = $customer->getData('account_number');
        return $accountNumber != null ? $accountNumber : "";
    }

    public function getSalesRep(User $adminUser)
    {
        return $adminUser->getFirstName();
    }

    public function getHeaders()
    {
        return [
            "Company Name",
            "Company Email",
            "Working Hours",
            "Company Website",
            "Company Phone Number",
            "Channel",
            "Sub Channel",
            "Customer Type",
            "Shipping Street",
            "Shipping City",
            "Shipping State",
            "Shipping Postal Code",
            "Shipping Country",
            "Billing Street",
            "Billing City",
            "Billing State",
            "Billing Postal Code",
            "Billing Country",
            "Salesforce Unique Identifier",
            "Company Id",
            "Sales Rep"
        ];
    }


}
