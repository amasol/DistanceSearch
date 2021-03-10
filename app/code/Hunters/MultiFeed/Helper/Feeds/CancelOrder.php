<?php


namespace Hunters\MultiFeed\Helper\Feeds;

class CancelOrder extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $resultPageFactory;
    protected $collectionFactory;
    protected $csv;
    protected $_orderItemCollection;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory,
        \Hunters\MultiFeed\Helper\Csv $csv,
        \Toppik\Sap\Model\ResourceModel\OrderItem\CollectionFactory  $orderItemCollection
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->csv = $csv;
        $this->_orderItemCollection = $orderItemCollection;
    }

    public function _getOrderDiscount($orderId) {
        $collection = $this->_orderItemCollection->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'discount' => 'item_discount'
            ]);
        $discount = 0.00;
        foreach ($collection as $item) {
            $discount += floatval($item->getDiscount());
        }
        return $discount;
    }

    public function _getItemsPrice($orderId) {
        $collection = $this->collectionFactory->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'item_price' => 'price'
            ]);
        $itemsPrice = [];
        foreach ($collection as $item) {
            $itemsPrice[] = floatval($item->getItemPrice());
        }
        return $itemsPrice;
    }

    public function generate($filterIds)
    {
        $collection = $this->collectionFactory
            ->create();

        $ids = implode(',', $filterIds);
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("main_table.order_id in ($ids)"));

        $collection->getSelect()
            ->where(new \Zend_Db_Expr('main_table.order_id not in (select order_id from sales_creditmemo)'))
            ->where(new \Zend_Db_Expr('main_table.parent_item_id is null'));

        $collection->getSelect()->join(
            ['o' => 'sales_order'],
            new \Zend_Db_Expr('o.entity_id = main_table.order_id and o.status = "canceled"'),
            []
        );

        $collection->getSelect()->joinLeft(
            ['s' => 'sales_shipment'],
            's.order_id = main_table.order_id',
            []
        );
        // $collection->getSelect()->joinLeft(
        //         ['scmi' => 'sales_creditmemo'],
        //         'scmi.order_id = main_table.order_id',
        //         []
        //     );


        $collection->getSelect()->joinLeft(
            ['mod' => 'microsite_order_detail'],
            'mod.order_id = o.entity_id',
            []
        );

        $collection->getSelect()->joinLeft(
            ['shipping' => 'sales_order_address'],
            new \Zend_Db_Expr('shipping.parent_id = main_table.order_id and shipping.address_type = "shipping"'),
            []
        );

        $collection->getSelect()->joinLeft(
            ['billing' => 'sales_order_address'],
            new \Zend_Db_Expr('billing.parent_id = main_table.order_id and billing.address_type = "billing"'),
            []
        );

        // $profiles = new \Zend_Db_Expr('(select sp.frequency_length, spo.*, if (b.child_id is not null, b.child_id, sp.sku) as sku,sp.sku as parent_sku from subscriptions_profiles_orders as spo left join subscriptions_profiles as sp on sp.profile_id = spo.profile_id left join sap_sku_breakdown as b on b.parent_id = sp.sku)');

        $profiles = new \Zend_Db_Expr('(select sp.frequency_length, spo.*, sp.sku as sku,sp.sku as parent_sku, b.child_id as child_sku from subscriptions_profiles_orders as spo left join subscriptions_profiles as sp on sp.profile_id = spo.profile_id left join sap_sku_breakdown as b on b.parent_id = sp.sku)');


        $collection->getSelect()->joinLeft(
            ['profile' => $profiles],
            'profile.order_id = main_table.order_id and profile.parent_sku = main_table.sku',
            []
        );

        // get media code from parent order
        $parentOrderByProfile = new \Zend_Db_Expr('(select profile_id, min(order_id) as parentOrderId from subscriptions_profiles_orders as spo2 group by profile_id )');
        $collection->getSelect()->joinLeft(
            ['parentOrder' => $parentOrderByProfile],
            'profile.profile_id = parentOrder.profile_id',
            []
        );
        $collection->getSelect()->joinLeft(
            ['mod2' => 'microsite_order_detail'],
            'mod2.order_id = parentOrder.parentOrderId',
            []
        );


        $psn = new \Zend_Db_Expr('(SELECT p.entity_id, p.row_id, p.sku, max(ps.value) as value
          FROM catalog_product_entity AS p
          LEFT JOIN (
          SELECT row_id, value
          FROM catalog_product_entity_varchar
          WHERE attribute_id = 525
          ) AS ps ON ps.row_id = p.row_id group by p.sku)');

        $collection->getSelect()->joinLeft(
            ['psn' => $psn],
            'psn.sku = main_table.sku',
            []
        );

        $collection
            ->getSelect()
            ->group(new \Zend_Db_Expr('main_table.order_id,main_table.sku,profile.frequency_length,main_table.price'))
            ->order('o.increment_id DESC');


        $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'entity_id' => 'o.entity_id',
                'increment_id' => 'o.increment_id',
                'created_at' => 'o.created_at',
                'updated_at' => 'o.updated_at',
                'coupon_code' => 'o.coupon_code',
                'source' => 'o.source',
                'grand_total' => 'o.grand_total',
                'shipping_amount' => 'o.shipping_amount',
                'shipping_tax_amount' => 'o.shipping_tax_amount',
                'customer_email' => 'o.customer_email',
                'ship_canceled_date' => 's.created_at',
                'media_code' => 'mod.media_code',
                'project_code' => 'mod.project_code',
                'parent_media_code' => 'mod2.media_code',
                'parent_project_code' => 'mod2.project_code',
                'psn_value' => new \Zend_Db_Expr('if(psn.value is not null,psn.value,main_table.sku)'),
                'qty_ordered' => 'main_table.qty_ordered',
                'qty_shipped' => 0,
                'parent_sku' => 'main_table.sku',
                'child_sku' => 'profile.child_sku',
                'item_price' => 'main_table.price',
                'order_status' => new \Zend_Db_Expr('IF(s.order_id is not null and o.status = "closed","return", o.status)'),
                'item_tax' => 'main_table.tax_amount',
                // 'child_sku' => 'main_table.item_sku',
                'profile_id' => 'profile.profile_id',
                'freq' => 'profile.frequency_length',
                'product_type' => 'main_table.product_type',
                // 'item_creditmemo_id' => 'scmi.increment_id',
                'sfirstname' => 'shipping.firstname',
                'slastname' => 'shipping.lastname',
                'sregion' => 'shipping.region',
                'spostcode' => 'shipping.postcode',
                'stelephone' => 'shipping.telephone',
                'scountry_id' => 'shipping.country_id',
                'scity' => 'shipping.city',
                'sstreet' => 'shipping.street',
                'bfirstname' => 'billing.firstname',
                'blastname' => 'billing.lastname',
                'bregion' => 'billing.region',
                'bpostcode' => 'billing.postcode',
                'btelephone' => 'billing.telephone',
                'bcountry_id' => 'billing.country_id',
                'bcity' => 'billing.city',
                'bstreet' => 'billing.street',
                'refund' => 'o.total_refunded',
		'prefix' => 'shipping.prefix',
                'discount' => 'o.discount_amount'
            ]);

        $skuMaxPrice = [];
        // add array of orders with profile_id
        foreach ($collection as $item) {
            if ($item->getProfileId()) {
                $ordersWithProfiles[] = $item->getEntityId();

                //remove profile_id from not subscription item
                $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()] = $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()] ?? [];
                if (!in_array($item->getItemPrice(), $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()])) {
                    $skuMaxPrice[$item->getEntityId()][$item->getPsnValue()][] = $item->getItemPrice();
                }

            }
        }

        $orders = [];
        $data[] = $this->getHeaders();

        foreach ($collection as $item) {

            $skipRow = '';
            $freight = '';
	    $discount = '';
	    $refund = '';
            if (!in_array($item->getEntityId(), $orders)) {
		$discount = abs($item->getDiscount());
		$refund = $item->getRefund();
                $skipRow = 1;
                $freight = $item->getShippingAmount() + $item->getShippingTaxAmount();
                $orders[] = $item->getEntityId();
            }

            $parentSku = empty($item->getChildSku()) ? '' :
                $item->getParentSku();

            $profileId = $item->getProfileId();
            if (isset($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) &&
                count($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) > 1 &&
                max($skuMaxPrice[$item->getEntityId()][$item->getPsnValue()]) == $item->getItemPrice()
            ){
                $profileId = '';
            }

            $data[] = [
                $item->getIncrementId(),
                $this->convertDate($item->getCreatedAt()),
                $this->convertDate($item->getUpdatedAt()),
                $freight,
                $item->getSource(),
                $parentSku,
                $item->getGrandTotal(),
                $item->getPsnValue(),
                intval($item->getQtyShipped()),
                intval($item->getQtyShipped()),
                $item->getItemPrice(),
                intval($item->getQtyOrdered()) * floatval($item->getItemPrice()),
                'Y',
                $item->getMediaCode() ?? $item->getParentMediaCode(),
                $item->getProjectCode() ?? $item->getParentProjectCode(),
                0,
                $item->getItemTax(),
                $item->getSfirstname(),
                $item->getSlastname(),
                $item->getSstreet(),
                '',
                $item->getScity(),
                $item->getSregion(),
                $item->getSpostcode(),
                $item->getStelephone(),
                $item->getScountryId(),

                $item->getBfirstname(),
                $item->getBlastname(),
                $item->getBstreet(),
                '',
                $item->getBcity(),
                $item->getBregion(),
                $item->getBpostcode(),
                // $item->getBtelephone(),
                // $item->getBcountryId(),
                // $item->getItemCreditmemoId(),
                '',
                $item->getOrderStatus(),
                $item->getCustomerEmail(),
                $profileId,
                $skipRow,
                $discount,
                $refund,
                $item->getPrefix()
            ];

        }

        return $this->csv->generate('multifeed_order_cancelled', $data);
    }


    public function getHeaders()
    {
        return [
            "Order Number",
            "Order Received",
            "Ship/Canceled Date",
            "Freight",
            "Order Source",
            "Offer Code",
            "Order Total",
            "Item #",
            "Quantity Ordered",
            "Quantity Shipped",
            "Unit Price",
            "Item Sum",
            "Taxable Y/N",
            "Promo Code",
            "Campaign of Promo Code",
            "Shipping Amount",
            "Sales Tax",
            "Ship to FName","Ship to LName","Ship to Addr1","Ship to Addr2","Ship to City","Ship to State","Ship to Zip","Ship to Phone","Ship to Country",
            "Bill to FName","Bill to LName","Bill to Addr1","Bill to Addr2","Bill to City","Bill to St","Bill to Zip",
            "Credit Memo Number",
            "Status",
            "Email",
            "Profile ID",
            "Skip Row",
            "Discount",
            "Refund",
            "Prefix"
        ];
    }


    public function convertDate($date, $format = 'd/m/Y')
    {
        return $date ? date($format, strtotime($date)) : '';
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
}
