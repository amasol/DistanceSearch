<?php
namespace Hunters\AvaCert\Helper;

class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $dataHelper;
    protected $customerRepositoryInterface;


    protected $params = [
      'submitToStack',
      'exposureZone',
      'expectedTaxCode',
      'pdf',
      'expirationDate'
    ];

    protected $params2validate = [
      'exposureZone',
      'expectedTaxCode',
      'pdf',
      'expirationDate'
    ];

    protected $key2param = [
      'submitToStack' => 'submit_to_stack',
      'exposureZone' => 'exposure_zone',
      'expectedTaxCode' => 'expected_tax_code',
      'expirationDate' => 'expiration_date'
    ];


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Data $dataHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Directory\Model\CountryFactory $countryFactory
    )
    {
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->countryFactory = $countryFactory;

    }

    public function getCustomerCerts($customerId)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://api.certcapture.com/v2/customers/$customerId/certificates");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);

      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeadersList());
      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response);
    }


  public function add($data)
  {
    $ch = curl_init();
    $postFields = '';

    foreach ($data as $key => $value) {

      if (!in_array($key, $this->params)) continue;

      if (in_array($key, $this->params2validate)) {
        $value = $this->{"get" . ucfirst($key)}($value);
      }

      $key = $this->key2param[$key] ?? $key;

      $postFields.= '&' . $key . '=' . $value;
    }

    $postFields.='&submit_to_stack=true&expiration_date=' . date("Y-m-d", strtotime(date("Y-m-d", time()) . " + 1 year"));
    $postFields = trim($postFields, '&');

    curl_setopt($ch, CURLOPT_URL, "https://api.certcapture.com/v2/certificates");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_POST, TRUE);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeadersList());

    $response = curl_exec($ch);
    
    file_put_contents(BP . '/var/log/data.log', print_r('add', true) . "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(BP . '/var/log/data.log', print_r($this->getCurlHeadersList(), true) . "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(BP . '/var/log/data.log', print_r($postFields, true) . "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(BP . '/var/log/data.log', print_r($response, true) . "\n", FILE_APPEND | LOCK_EX);
    
    curl_close($ch);

    $res = json_decode($response);

    if (isset($res->id) && isset($data['customers']) && !empty($data['customers'])){
      $customers = explode(',', $data['customers']);
      $this->addCert2Customer($res->id, $customers);
    }

    return $res;

  }


  public function addCert2Customer($certId, $customers)
  {

    foreach ($customers as $customerId) {

      // create customer if does not exists
      $this->createCustomer($customerId);


      $ch = curl_init();
      $postFields = 'certificates=[{"id":' . $certId . '}]';
      curl_setopt($ch, CURLOPT_URL, "https://api.certcapture.com/v2/customers/$customerId/certificates");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeadersList());
      $response = curl_exec($ch);
      curl_close($ch);
    }
  }

  public function createCustomer($customerId)
  {
      $ch = curl_init();

      // move logic
      $customer = $this->customerRepositoryInterface->getById($customerId);
      $om = \Magento\Framework\App\ObjectManager::getInstance();

      $cdata = [];
      $cdata['name'] = $customer->getFirstName() . ' ' . $customer->getLastName();
      $email = $customer->getEmail();
      $addresses = $customer->getAddresses();

      $postFields = "email_address=$email&customer_number=$customerId";

      foreach ($addresses as $a) {
        $cdata['city'] = $a->getCity() ?? '';

        if ($a->getRegionId()) {
          $region = $om->create('Magento\Directory\Model\Region')
                          ->load($a->getRegionId());
          if ($region)
            $cdata['state'] = json_encode(['name' => $region->getName()]);
        }

        $cdata['country'] = json_encode(['name' =>
          $this->getCountryName($a->getCountryId()) ?? '']);
        $cdata['zip'] = $a->getPostcode() ?? '';
        $cdata['fax_number'] = $a->getFax() ?? '';
        $cdata['phone_number'] = $a->getTelephone() ?? '';
        $street = $a->getStreet() ?? '';

        if (is_array($street)) {
          $cdata['address_line1'] = $street[0];
          $cdata['address_line2'] = $street[1] ?? '';
        }
      }

      foreach ($cdata as $k => $v) {
        if ($v) {
          $postFields.= '&' . $k . '=' . $v;
        }
      }

      curl_setopt($ch, CURLOPT_URL, "https://api.certcapture.com/v2/customers");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeadersList());
      $response = curl_exec($ch);

      curl_close($ch);
  }

  public function getCustomerIdFromNumber($number)
  {
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, "https://api.certcapture.com/v2/customers/$number");
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  	curl_setopt($ch, CURLOPT_HEADER, FALSE);

  	curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeadersList());

  	$response = curl_exec($ch);
  	$res = json_decode($response);

  	return $res->id ?? null;

  }

  public function getPdf($value)
  {
    $parts = explode(',', $value, 2);

    return isset($parts[1]) ? $this->_rawurlencode($parts[1]) :
      $this->_rawurlencode($value);
  }

  public function getExposureZone($value)
  {
    return '{"id":' . $value . '}';
  }

  public function getExpectedTaxCode($value)
  {
    return '{"id":' . $value . '}';
  }

  public function getExpirationDate($value)
  {
    return urlencode($value);
  }


  public function _rawurlencode($string) {
    $str = str_replace(' ','+',$string);
    $string = rawurlencode($str);
    return $string;
  }

  public function getCurlHeadersList()
  {
    return [
        "x-client-id: " . $this->dataHelper->getCompanyId(),
        "x-customer-primary-key: customer_number",
        "Authorization: Basic " . $this->dataHelper->getBasicAuthToken(),
        "Content-Type: application/x-www-form-urlencoded"
    ];
  }

  public function getCountryName($countryCode){
    $country = $this->countryFactory->create()->loadByCode($countryCode);
    return $country->getName();
  }


}
