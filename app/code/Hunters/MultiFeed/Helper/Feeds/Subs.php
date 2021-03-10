<?php


namespace Hunters\MultiFeed\Helper\Feeds;

class Subs extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $resultPageFactory;
    protected $collectionFactory;
    protected $csv;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Toppik\Subscriptions\Model\ResourceModel\Profile\CollectionFactory $collectionFactory,
        \Hunters\MultiFeed\Helper\Csv $csv
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->csv = $csv;
    }

    /**
     * New, End, Update
    */
    public function generate($filterProfileIds, $storeId = null)
    {

      $new[] = $this->getHeaders();
      $end[] = $this->getHeaders();
      $update[] = $this->getHeaders();

      $emails_new = [];
      $emails_end = [];
      $emails_update = [];


      $unique_new = [];
      $unique_end = [];
      $unique_update = [];


      // $unique = [];


      // get sap form skus
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
      $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
      $connection = $resource->getConnection();
      $sql = "SELECT p.sku, MAX(ps.value) as value FROM catalog_product_entity AS p LEFT JOIN ( SELECT row_id, value FROM catalog_product_entity_varchar WHERE attribute_id = 525) AS ps ON ps.row_id = p.row_id GROUP BY p.sku";
      $skuValue= $connection->fetchAll($sql);

      $skuVal = [];
      foreach ($skuValue as $sku) {
        if (isset($sku['value']) && $sku['value']) {
          $skuVal[$sku['sku']] = $sku['value'];
        }
      }

      foreach ($this->getSubsCollection($filterProfileIds) as $profile) {

        switch ($profile->getStatus()) {
          case 'suspended':
            $data = &$update;
            $emails = &$emails_update;
            $unique = &$unique_update;
            break;
          case 'cancelled':
            $data = &$end;
            $emails = &$emails_end;
            $unique = &$unique_end;
            break;
          case 'active':
            $data = &$new;
            $emails = &$emails_new;
            $unique = &$unique_new;
            break;
        }

        if (!isset($data)) continue;

        $skipRow = '';
        if (!in_array($profile->getEmail(), $emails)) {
          $skipRow = 1;

          if ($profile->getEmail()) {
            $emails[] = $profile->getEmail();
          }
        }

        $products = explode(
          ',',
          $profile->getChildSku()
        );

        foreach ($products as $product) {
          // if (count($products) > 1) {
          //   var_dump($products);
          //   echo $profile->getProfileId();
          //   echo $profile->getStatus();
          //
          // }
          $product = $skuVal[$product] ?? $product;
          if ( isset($unique[strval($profile->getProfileId())]) &&
               is_array($unique[strval($profile->getProfileId())]) &&
               in_array($product, $unique[strval($profile->getProfileId())])
          ) {
            continue;
          } else {
            $unique[strval($profile->getProfileId())][] = $product;
          }

          $data[] = [
            $profile->getSfirstname(),
            $profile->getSlastname(),
            $profile->getSstreet(),
            '',
            $profile->getScity(),
            $profile->getSregion(),
            $profile->getSpostcode(),
            $profile->getStelephone(),
            $profile->getScountryId(),
            $profile->getBfirstname(),
            $profile->getBlastname(),
            $profile->getBstreet(),
            '',
            $profile->getBcity(),
            $profile->getBregion(),
            $profile->getBpostcode(),
            $profile->getBtelephone(),
            $profile->getBcountryId(),
            $profile->getEmail(),
            $profile->getProfileId(),
            $this->convertDate($profile->getStartDate()),
            $this->convertDate($profile->getCancelledAt()),
            $profile->getStatus(),
            'Toppik',
            $product,
            '',
            '',
            $skipRow,
            $profile->getFrequencyLength() / 3600 / 24
          ];

          $skipRow = '';
        }

        // if (count($products) > 1) {
        //   var_dump($data);
        //   die;
        // }

        unset($data);
        unset($emails);
        unset($unique);
      }

      return [
        $this->csv->generate('multifeed_subs_new', $new, $storeId),
        $this->csv->generate('multifeed_subs_update', $update, $storeId),
        $this->csv->generate('multifeed_subs_end', $end, $storeId)
      ];


      // $this->renderHtmlTable(
      //   $this->getHeaders(),
      //   $data
      // );
    }



    public function getSubsCollection($filterProfileIds)
    {
        $collectionFactory = $this->collectionFactory
          ->create();

        $ids = implode(',', $filterProfileIds);
        $collectionFactory->getSelect()
          ->where(new \Zend_Db_Expr("main_table.profile_id in ($ids)"));

        $collectionFactory->getSelect()
          ->joinLeft(
                ['customer_entity' => 'customer_entity'],
                'customer_entity.entity_id = main_table.customer_id',
                ['customer_entity.email as email']
            );

      $orderProfile = new \Zend_Db_Expr('(select profile_id, min(order_id) as order_id from subscriptions_profiles_orders group by order_id)');
      $collectionFactory->getSelect()->joinLeft(
              ['spo' => $orderProfile],
              'spo.profile_id = main_table.profile_id',
              ['spo.order_id as order_id']
          );

        $collectionFactory->getSelect()
          ->joinLeft(
                ['so' => 'sales_order'],
                'so.entity_id = order_id',
                ['so.coupon_code as coupon_code']
            );



        $billing = new \Zend_Db_Expr('(SELECT profile_id,firstname as bfirstname,lastname as blastname, region as bregion, postcode as bpostcode,telephone as btelephone,country_id as bcountry_id, city as bcity, street as bstreet from subscriptions_profiles_address where
        address_type="billing" group by profile_id)');

        $collectionFactory->getSelect()->joinLeft(
                ['spa' => $billing],
                'spa.profile_id = main_table.profile_id',
                [new \Zend_Db_Expr('spa.bfirstname,spa.blastname,spa.bregion,spa.bpostcode,spa.btelephone,spa.bcountry_id,spa.bcity,spa.bstreet')]
            );

        $shipping = new \Zend_Db_Expr('(SELECT profile_id,firstname as sfirstname,lastname as slastname, region as sregion, postcode as spostcode,telephone as stelephone,country_id as scountry_id, city as scity, street as sstreet from subscriptions_profiles_address where
        address_type="shipping" group by profile_id)');

        $collectionFactory->getSelect()->joinLeft(
                ['spa2' => $shipping],
                'spa2.profile_id = main_table.profile_id',
                [new \Zend_Db_Expr('spa2.sfirstname,spa2.slastname,spa2.sregion,spa2.spostcode,spa2.stelephone,spa2.scountry_id,spa2.scity,spa2.sstreet')]
            );


      $sap = new \Zend_Db_Expr('(SELECT p.entity_id, p.row_id, p.sku, MAX(ps.value) as value FROM catalog_product_entity AS p LEFT JOIN ( SELECT row_id, value FROM catalog_product_entity_varchar WHERE attribute_id = 525) AS ps ON ps.row_id = p.row_id GROUP BY p.sku)');

      $collectionFactory->getSelect()->joinLeft(
              ['sap' => $sap],
              'sap.sku = main_table.sku',
              ['sap.value as sap_number']
          );

      $collectionFactory->getSelect()
        ->joinLeft(
              ['ssb' => 'sap_sku_breakdown'],
              'ssb.parent_id = main_table.sku',
              [new \Zend_Db_Expr("if (sap.value is not null,sap.value,GROUP_CONCAT(ssb.child_id)) as child_sku")]
          );

        // $collectionFactory->getSelect()
        // ->reset(\Zend_Db_Select::COLUMNS)
        // ->columns([
        //     'child_sku' => new \Zend_Db_Expr("if (sap.value is not null,sap.value,GROUP_CONCAT(ssb.child_id))")
        //   ]);
        $collectionFactory
          ->getSelect()
          ->group(new \Zend_Db_Expr('main_table.profile_id'))
          ->order('so.increment_id DESC');

        return $collectionFactory;
    }

    public function renderHtmlTable(array $headers, array $data)
    {
      $html = '<table><tr>';

      foreach ($headers as $header) {
        $html.='<th>' . $header . '</th>';
      }

      $html.='</tr>';

      foreach ($data as $row) {
        $html.='<tr>';
        foreach ($row as $field) {
          $html.='<td>' . $field . '</td>';
        }
        $html.='</tr>';
      }

      $html.='</table>';
      $html.='<style>
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
        }
        th, td {
          padding: 5px;
          text-align: left;
        }
        </style>';

      echo $html;
    }

    public function getHeaders()
    {

      return [
        'Ship to FName','Ship to LName','Ship to Addr1','Ship to Addr2','Ship to City','Ship to State','Ship to Zip','Ship to Phone','Ship to Country',
        'Bill to FName','Bill to LName','Bill to Addr1','Bill to Addr2','Bill to City','Bill to St','Bill to Zip','Bill to Phone','Bill to Country',
        'Email','Profile ID','Start date','End date','Status','Brand','Product','Campaign','Tactic','Skip','Frequency'
      ];
    }

    public function convertDate($date, $format = 'Y-m-d')
    {
      return $date ? date($format, strtotime($date)) : '';
    }
}
