<?php
namespace Hunters\AvaCert\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $encryptor;

    public function __construct(
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
      $this->scopeConfig = $scopeConfig;
      $this->encryptor = $encryptor;
  }

  protected $exposureZones = array (
    0 =>
    array (
      'id' => 141,
      'name' => 'Alabama',
      'tag' => 'EZ_US_AL',
    ),
    1 =>
    array (
      'id' => 140,
      'name' => 'Alaska',
      'tag' => 'EZ_US_AK',
    ),
    2 =>
    array (
      'id' => 138,
      'name' => 'Arizona',
      'tag' => 'EZ_US_AZ',
    ),
    3 =>
    array (
      'id' => 137,
      'name' => 'Arkansas',
      'tag' => 'EZ_US_AR',
    ),
    4 =>
    array (
      'id' => 136,
      'name' => 'California',
      'tag' => 'EZ_US_CA',
    ),
    5 =>
    array (
      'id' => 135,
      'name' => 'Colorado',
      'tag' => 'EZ_US_CO',
    ),
    6 =>
    array (
      'id' => 134,
      'name' => 'Connecticut',
      'tag' => 'EZ_US_CT',
    ),
    7 =>
    array (
      'id' => 133,
      'name' => 'Delaware',
      'tag' => 'EZ_US_DE',
    ),
    8 =>
    array (
      'id' => 132,
      'name' => 'District Of Columbia',
      'tag' => 'EZ_US_DC',
    ),
    9 =>
    array (
      'id' => 131,
      'name' => 'Florida',
      'tag' => 'EZ_US_FL',
    ),
    10 =>
    array (
      'id' => 130,
      'name' => 'Georgia',
      'tag' => 'EZ_US_GA',
    ),
    11 =>
    array (
      'id' => 128,
      'name' => 'Hawaii',
      'tag' => 'EZ_US_HI',
    ),
    12 =>
    array (
      'id' => 127,
      'name' => 'Idaho',
      'tag' => 'EZ_US_ID',
    ),
    13 =>
    array (
      'id' => 126,
      'name' => 'Illinois',
      'tag' => 'EZ_US_IL',
    ),
    14 =>
    array (
      'id' => 125,
      'name' => 'Indiana',
      'tag' => 'EZ_US_IN',
    ),
    15 =>
    array (
      'id' => 124,
      'name' => 'Iowa',
      'tag' => 'EZ_US_IA',
    ),
    16 =>
    array (
      'id' => 123,
      'name' => 'Kansas',
      'tag' => 'EZ_US_KS',
    ),
    17 =>
    array (
      'id' => 122,
      'name' => 'Kentucky',
      'tag' => 'EZ_US_KY',
    ),
    18 =>
    array (
      'id' => 121,
      'name' => 'Louisiana',
      'tag' => 'EZ_US_LA',
    ),
    19 =>
    array (
      'id' => 120,
      'name' => 'Maine',
      'tag' => 'EZ_US_ME',
    ),
    20 =>
    array (
      'id' => 119,
      'name' => 'Maryland',
      'tag' => 'EZ_US_MD',
    ),
    21 =>
    array (
      'id' => 118,
      'name' => 'Massachusetts',
      'tag' => 'EZ_US_MA',
    ),
    22 =>
    array (
      'id' => 117,
      'name' => 'Michigan',
      'tag' => 'EZ_US_MI',
    ),
    23 =>
    array (
      'id' => 116,
      'name' => 'Minnesota',
      'tag' => 'EZ_US_MN',
    ),
    24 =>
    array (
      'id' => 115,
      'name' => 'Mississippi',
      'tag' => 'EZ_US_MS',
    ),
    25 =>
    array (
      'id' => 114,
      'name' => 'Missouri',
      'tag' => 'EZ_US_MO',
    ),
    26 =>
    array (
      'id' => 113,
      'name' => 'Montana',
      'tag' => 'EZ_US_MT',
    ),
    27 =>
    array (
      'id' => 112,
      'name' => 'Nebraska',
      'tag' => 'EZ_US_NE',
    ),
    28 =>
    array (
      'id' => 111,
      'name' => 'Nevada',
      'tag' => 'EZ_US_NV',
    ),
    29 =>
    array (
      'id' => 110,
      'name' => 'New Hampshire',
      'tag' => 'EZ_US_NH',
    ),
    30 =>
    array (
      'id' => 109,
      'name' => 'New Jersey',
      'tag' => 'EZ_US_NJ',
    ),
    31 =>
    array (
      'id' => 108,
      'name' => 'New Mexico',
      'tag' => 'EZ_US_NM',
    ),
    32 =>
    array (
      'id' => 107,
      'name' => 'New York',
      'tag' => 'EZ_US_NY',
    ),
    33 =>
    array (
      'id' => 106,
      'name' => 'North Carolina',
      'tag' => 'EZ_US_NC',
    ),
    34 =>
    array (
      'id' => 105,
      'name' => 'North Dakota',
      'tag' => 'EZ_US_ND',
    ),
    35 =>
    array (
      'id' => 103,
      'name' => 'Ohio',
      'tag' => 'EZ_US_OH',
    ),
    36 =>
    array (
      'id' => 102,
      'name' => 'Oklahoma',
      'tag' => 'EZ_US_OK',
    ),
    37 =>
    array (
      'id' => 101,
      'name' => 'Oregon',
      'tag' => 'EZ_US_OR',
    ),
    38 =>
    array (
      'id' => 100,
      'name' => 'Pennsylvania',
      'tag' => 'EZ_US_PA',
    ),
    39 =>
    array (
      'id' => 99,
      'name' => 'Puerto Rico',
      'tag' => 'EZ_US_PR',
    ),
    40 =>
    array (
      'id' => 98,
      'name' => 'Rhode Island',
      'tag' => 'EZ_US_RI',
    ),
    41 =>
    array (
      'id' => 97,
      'name' => 'South Carolina',
      'tag' => 'EZ_US_SC',
    ),
    42 =>
    array (
      'id' => 96,
      'name' => 'South Dakota',
      'tag' => 'EZ_US_SD',
    ),
    43 =>
    array (
      'id' => 95,
      'name' => 'Tennessee',
      'tag' => 'EZ_US_TN',
    ),
    44 =>
    array (
      'id' => 94,
      'name' => 'Texas',
      'tag' => 'EZ_US_TX',
    ),
    45 =>
    array (
      'id' => 92,
      'name' => 'Utah',
      'tag' => 'EZ_US_UT',
    ),
    46 =>
    array (
      'id' => 91,
      'name' => 'Vermont',
      'tag' => 'EZ_US_VT',
    ),
    47 =>
    array (
      'id' => 90,
      'name' => 'Virginia',
      'tag' => 'EZ_US_VA',
    ),
    48 =>
    array (
      'id' => 89,
      'name' => 'Washington',
      'tag' => 'EZ_US_WA',
    ),
    49 =>
    array (
      'id' => 88,
      'name' => 'West Virginia',
      'tag' => 'EZ_US_WV',
    ),
    50 =>
    array (
      'id' => 87,
      'name' => 'Wisconsin',
      'tag' => 'EZ_US_WI',
    ),
    51 =>
    array (
      'id' => 86,
      'name' => 'Wyoming',
      'tag' => 'EZ_US_WY',
    ),
  );

  protected $exemptReasons = array (
  0 =>
  array (
    'id' => 56,
    'name' => 'AGRICULTURE',
    'tag' => 'TC_OO_AGRI',
  ),
  1 =>
  array (
    'id' => 61,
    'name' => 'DIRECT PAY',
    'tag' => 'TC_OO_DPAY',
  ),
  2 =>
  array (
    'id' => 99,
    'name' => 'EDUCATIONAL ORG',
    'tag' => 'TC_OO_EDUC',
  ),
  3 =>
  array (
    'id' => 16,
    'name' => 'EXPOSURE',
    'tag' => 'TC_EX_EXPO',
  ),
  4 =>
  array (
    'id' => 19,
    'name' => 'EXPOSURE: EXPIRED CERT',
    'tag' => 'TC_EX_EXPI',
  ),
  5 =>
  array (
    'id' => 21,
    'name' => 'EXPOSURE: INDIRECT CERT',
    'tag' => 'TC_EX_INDI',
  ),
  6 =>
  array (
    'id' => 20,
    'name' => 'EXPOSURE: INVALID CERT',
    'tag' => 'TC_EX_INVA',
  ),
  7 =>
  array (
    'id' => 18,
    'name' => 'EXPOSURE: MISSING CERT',
    'tag' => 'TC_EX_MISS',
  ),
  8 =>
  array (
    'id' => 1,
    'name' => 'EXPOSURE: NON-DELIVERABLE',
    'tag' => 'TC_EX_NOND',
  ),
  9 =>
  array (
    'id' => 62,
    'name' => 'FEDERAL GOV',
    'tag' => 'TC_GV_FEDE',
  ),
  10 =>
  array (
    'id' => 64,
    'name' => 'INDUSTRIAL PROD/MANUFACTURERS',
    'tag' => 'TC_OO_MANU',
  ),
  11 =>
  array (
    'id' => 65,
    'name' => 'LOCAL GOVERNMENT',
    'tag' => 'TC_GV_LOCA',
  ),
  12 =>
  array (
    'id' => 96,
    'name' => 'LONG-TERM RENTAL',
    'tag' => 'TC_OO_LODG',
  ),
  13 =>
  array (
    'id' => 66,
    'name' => 'MEDICAL',
    'tag' => 'TC_OO_MEDI',
  ),
  14 =>
  array (
    'id' => 67,
    'name' => 'NON-DELIVERABLE',
    'tag' => 'TC_OO_NOND',
  ),
  15 =>
  array (
    'id' => 25,
    'name' => 'NON-NEXUS: EXPIRED CERT',
    'tag' => 'TC_NN_EXPI',
  ),
  16 =>
  array (
    'id' => 22,
    'name' => 'NON-NEXUS: EXPOSURE',
    'tag' => 'TC_NN_EXPO',
  ),
  17 =>
  array (
    'id' => 27,
    'name' => 'NON-NEXUS: INDIRECT CERT',
    'tag' => 'TC_NN_INDI',
  ),
  18 =>
  array (
    'id' => 26,
    'name' => 'NON-NEXUS: INVALID CERT',
    'tag' => 'TC_NN_INVA',
  ),
  19 =>
  array (
    'id' => 24,
    'name' => 'NON-NEXUS: MISSING CERT',
    'tag' => 'TC_NN_MISS',
  ),
  20 =>
  array (
    'id' => 23,
    'name' => 'NON-NEXUS: NON-DELIVERABLE',
    'tag' => 'TC_NN_NOND',
  ),
  21 =>
  array (
    'id' => 83,
    'name' => 'POLLUTION CONTROL',
    'tag' => 'TC_EV_POLL',
  ),
  22 =>
  array (
    'id' => 100,
    'name' => 'RELIGIOUS/EDUCATIONAL ORG',
    'tag' => 'TC_OO_RELI',
  ),
  23 =>
  array (
    'id' => 101,
    'name' => 'RELIGIOUS ORG',
    'tag' => 'TC_OO_RELG',
  ),
  24 =>
  array (
    'id' => 71,
    'name' => 'RESALE',
    'tag' => 'TC_OO_RESA',
    'include' => true
  ),
  25 =>
  array (
    'id' => 102,
    'name' => 'STATE GOV',
    'tag' => 'TC_GV_STAT',
  ),
  26 =>
  array (
    'id' => 72,
    'name' => 'STATE/LOCAL GOV',
    'tag' => 'TC_GV_STLC',
  ),
  27 =>
  array (
    'id' => 73,
    'name' => 'TAXABLE',
    'tag' => 'TC_OO_TAXA',
  ),
);

  public function getExposureZonesList()
  {
    return $this->exposureZones;
  }

  public function getExposureZonesOptions()
  {
    $options = [];
    foreach ($this->exposureZones as $zone) {
      $options[] = [
        'label' => $zone['name'],
        'value' => $zone['id'],
        []
      ];
    }

    return $options;
  }

  public function getExemptReasonsList()
  {
    return $this->exemptReasons;
  }

  public function getExemptReasonsOptions()
  {
    $options = [];

    foreach ($this->exemptReasons as $zone) {
      if (!isset($zone['include']))
        continue;

      $options[] = [
        'label' => $zone['name'],
        'value' => $zone['id'],
        []
      ];
    }

    return $options;
  }

  public function getBasicAuthToken()
  {
      $login = $this->scopeConfig->getValue('avacert/config/login');
      $password = $this->scopeConfig->getValue('avacert/config/password');

      if (!$login || !$password)
        return;

      return base64_encode(
        $this->encryptor->decrypt($login) . ':' .
        $this->encryptor->decrypt($password)
      );

  }

  public function getCompanyId()
  {
      return $this->scopeConfig->getValue('avacert/config/company_id');
  }

}
