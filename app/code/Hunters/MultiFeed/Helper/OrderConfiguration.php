<?php


namespace Hunters\MultiFeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Toppik\Sap\Model\ResourceModel\OrderItem\CollectionFactory;
use Hunters\MultiFeed\Helper\Csv;
use Toppik\Sap\Model\ResourceModel\OrderItem\CollectionFactory as SapCollection;
use Toppik\Sap\Model\OrderItem;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Item;
use Magento\Framework\App\ResourceConnection;
use Toppik\Refund\Model\ResourceModel\Sales\Creditmemo\Item\CollectionFactory as RefundCollectin;
use Toppik\Refund\Helper\Data;
use Magento\User\Model\UserFactory;
use Magento\Company\Api\CompanyManagementInterface;

class OrderConfiguration  extends AbstractHelper
{
    const COMPLETE_ORDER_STATUS = 'complete';
    protected $collectionFactory;
    protected $csv;

    protected $_orderItemCollection;
    protected $_orderItem;


    protected $_order;
    protected $_shipping;
    protected $_item;
    protected $_resourceConnection;
    protected $_creditMemoItem;
    protected $_helper;
    protected $_adminUser;
    protected $_company;

    public function __construct(
        CollectionFactory $collectionFactory,
        Csv $csv,
        SapCollection  $orderItemCollection,
        OrderItem $orderItem,
        Order $order,
        Shipment $shipment,
        Item $item,
        ResourceConnection $resourceConnection,
        RefundCollectin $itemCreditmemo,
        Data $helper,
        UserFactory $user,
        CompanyManagementInterface $companyManagement
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->csv = $csv;
        $this->_orderItemCollection = $orderItemCollection;
        $this->_orderItem = $orderItem;

        $this->_order = $order;
        $this->_shipping = $shipment;
        $this->_item = $item;
        $this->_resourceConnection = $resourceConnection;
        $this->_creditMemoItem = $itemCreditmemo;
        $this->_helper = $helper;
        $this->_adminUser = $user->create();
        $this->_company = $companyManagement;
    }

    public function getQryOrderedByOrderId($orderId, $productId) {
        $sql = "SELECT i.qty_ordered FROM sap_order_queue_items AS sap ".
            "JOIN sales_order_item AS i ON sap.item_id = i.item_id ".
            " WHERE sap.order_id = :orderId AND sap.item_material_number = :productId;";
        $bind = [
            'orderId' => $orderId,
            'productId' => $productId,
        ];
        return $this->_getConnection()->fetchOne($sql, $bind);
    }

    private function _getConnection() {
        static $connection;
        if (empty($connection)) {
            $connection = $this->_resourceConnection->getConnection();
        }
        return $connection;
    }

    public function getCompanyNameByCustomerId($customerId) {
        $sql = "select entity_id from company where entity_id = ".
            "(select company_id from company_advanced_customer_entity where customer_id = :customer_id);";
        $bind = [
            'customer_id' => $customerId,
        ];
        return $this->_getConnection()->fetchOne($sql, $bind);
    }

    public function getCustomerId(Order $order) {
        return $order->getData('customer_id');
    }

    public function getParenSku($orderId, $productId) {
        $sql = "select if (sap.item_sku = i.sku, ' ', i.sku) as res from sap_order_queue_items as sap ".
            "join sales_order_item as i on sap.item_id = i.item_id ".
            " where sap.order_id = :orderId and sap.item_material_number = :productId";
        $bind = [
            'orderId' => $orderId,
            'productId' => $productId,
        ];
        return $this->_getConnection()->fetchOne($sql, $bind);
    }

    public function getMediaCodeSql($orderId) {
                
        $sql = "select media_code from microsite_order_detail where order_id = :orderId";
        $bind = [
            'orderId' => $orderId,
        ];
        return $this->_getConnection()->fetchOne($sql, $bind);
    }

    public function getProjectCodeSql($orderId) {
                
        $sql = "select project_code from microsite_order_detail where order_id = :orderId";
        $bind = [
            'orderId' => $orderId,
        ];
        return $this->_getConnection()->fetchOne($sql, $bind);
    }

    public function _checkOrderInSubsProfileOrders($orderId) {
        $sql = "select profile_id from subscriptions_profiles_orders where order_id = :orderId";
        $bind = [
            'orderId' => $orderId
        ];
        return count($this->_getConnection()->fetchAll($sql, $bind)) == 0 ? true : false ;
    }

    public function _checkOrderInSalesCreditmemo($orderId) {
        $sql = "select entity_id from sales_creditmemo where order_id = :orderId";
        $bind = [
            'orderId' => $orderId
        ];
        return count($this->_getConnection()->fetchAll($sql, $bind)) == 0 ? true : false ;
    }

    public function _getOrderDiscount($orderId) {
        $collection = $this->_orderItemCollection->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'discount' => 'item_discount'
            ]);
        $discount = [];
        foreach ($collection as $item) {
            $discount[] = floatval($item->getDiscount());
        }
        return $discount;
    }

    public function _getItemRefund($orderId, $sku =null) {
        try {
            $collection = $this->_creditMemoItem->create();
            $collection->getSelect()->where(new \Zend_Db_Expr("order_id = $orderId and sku = '$sku'"))
                ->columns(['price' => 'price']);
            $refund = 0.00;
            foreach ($collection as $item) {
                $refund += floatval($item->getPrice());
            }
            return $refund;
        } catch (\Exception $e) {
            file_put_contents(BP . '/var/log/multiFeed.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    public function _getQryShipped($orderId) {
        $collection = $this->_orderItemCollection->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'qty' => 'item_qty'
            ]);
        $qty = [];
        foreach ($collection as $item) {
            $qty[] = intval($item->getQty());
        }
        return $qty;
    }

    public function _getItemsPrice($orderId) {
        $collection = $this->_orderItemCollection->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'item_price' => 'item_price'
            ]);
        $itemsPrice = [];
        foreach ($collection as $item) {
            $itemsPrice[] = floatval($item->getItemPrice());
        }
        return $itemsPrice;
    }

    public function _getItemsTax($orderId) {
        $collection = $this->_orderItemCollection->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'item_tax' => 'item_tax'
            ]);
        $itemsTax = [];
        foreach ($collection as $item) {
            $itemsTax[] = floatval($item->getItemTax());
        }
        return $itemsTax;
    }

    public function _getPsnValue($orderId) {
        $collection = $this->_orderItemCollection->create();
        $collection->getSelect()
            ->where(new \Zend_Db_Expr("order_id = $orderId"))
            ->columns([
                'item_material_number' => 'item_material_number'
            ]);
        $itemMaterialNumber = [];
        foreach ($collection as $item) {
            $itemMaterialNumber[] = $item->getItemMaterialNumber();
        }
        return $itemMaterialNumber;
    }

    public function getEntityId(Order $order) {
        return $order->getEntityId();
    }

    public function getIncrementId(Order $order) {
        return $order->getIncrementId();
    }

    public function getCreateDate(Order $order) {
        return $order->getCreatedAt();
    }

    public function getShippingData(Shipment $shipment) {
        return $shipment->getCreatedAt();
    }

    public function getFreight(Order $order) {
        return $order->getShippingAmount() + $order->getShippingTaxAmount();
    }

    public function getOrderSource(Order $order) {
        return $order->getData('merchant_source');
    }

    public function getOrderGrandTotal(Order $order) {
        return $order->getGrandTotal();
    }

    public function getQtyOrder(Item $item) {
        return $item->getQtyOrdered();
    }

    public function getPromoCode(Detail $orderDetail) {
        return $orderDetail->getData('media_code');
    }

    public function getProjectCode(Detail $orderDetail) {
        return $orderDetail->getData('project_code');
    }

    public function getShippingAddress(Order $order) {
        return $order->getShippingAddress();
    }

    public function getBillingAddress(Order $order) {
        return $order->getBillingAddress();
    }

    public function getOrderStatus(Order $order) {
        return $order->getStatus();
    }

    public function getCustomerEmail(Order $order) {
        return $order->getCustomerEmail();
    }

    public function getRefund(Order $order) {
        return $order->getTotalRefunded();
    }

    public function getCouponCode(Order $order) {
        return $order->getCouponCode();
    }

    public function getAdminId(Order $order) {
        return $order->getData('admin_id');
    }

    public function generatorOrderData($itemPrice, $itemTax, $psnValue, $qtyShipped, $discount) {
        for ($i = 0; $i < count($itemPrice); $i++) {
            yield [
                'itemPrice' => $itemPrice[$i],
                'itemTax' =>$itemTax[$i],
                'itemMaterialNumber' => $psnValue[$i],
                'qtyShipped' => $qtyShipped[$i],
                'discount' => $discount[$i]
            ];
        }
    }

    public function getHeaders()
    {
        return [
            "Order Number",
            "Order Date",
            "Order Received",
            "Ship/Canceled Date",
            "Offer Code",
            "Item No.",
            "Quantity Ordered",
            "Unit Price",
            "Total Sale per Line",
            "Quantity Shipped",
            "Sales Tax",
            "Order Total",
            "Media",
            "Project",
            "Status",
            "CompanyId",
            "Freight",
            "OriginalOrder",
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
            "Order Name",
            "Sales Rep",
            "skip_row",
            "tactic_code",
            "Discount"
//            "Refund"
        ];
    }

    public function convertDate($date, $format = 'Y-m-d')
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
