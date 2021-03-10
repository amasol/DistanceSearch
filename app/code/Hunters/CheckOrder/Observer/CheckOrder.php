<?php

namespace Hunters\CheckOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckOrder implements ObserverInterface
{
    const is_downloadable = "downloadable";

    protected $cart;
    protected $logger;
    protected $adminOrder;


    /**
     * Data constructor.
     *
     * @param \Psr\Log\LoggerInterface                                 $logger Logger
     * @param array                                                    $data Array with data
     */
    public function __construct(
        \Magento\Backend\Model\Session\Quote $adminOrder,
        \Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger, array $data = [])
    {
        $this->cart = $cart;
        $this->logger = $logger;
        $this->adminOrder = $adminOrder;
    }

    public function execute(Observer $observer)
    {
        try {
            //get information about order
            $order = $observer->getEvent()->getOrder();
            $orderId = $order->getIncrementId();
            if ($this->cart->getQuote()->getEntityId() == null) {
                // get all item in customer order
                // when create order in admin side
                $product_info = $this->adminOrder->getQuote()->getAllVisibleItems();
            } else {
                // get all item in customer order
                // when create order in customer side
                $product_info = $this->cart->getQuote()->getItemsCollection();
            }
            $countDownloadableItem = 0;
            // count Downloadable item in order
            foreach ($product_info as $item) {
                if ($item->getProductType() == self::is_downloadable ) {
                    $countDownloadableItem++;
                }
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('sales_order');
            // if all product in order in's downloadable set flag 1
            $value = (count($product_info) == $countDownloadableItem ? 1 : 0);
            try {
                $sql = 'UPDATE '. $tableName .' SET is_downloadable = '. $value .' where increment_id = '. $orderId;
                $resulst = $connection->fetchAll($sql);
            } catch (\Exception $exception) {
                $this->logger->debug($exception->getMessage());
            }
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
    }
}
