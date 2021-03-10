<?php
namespace Hunters\AddPurchaseOrder\Observer;

use Magento\Framework\App\RequestInterface;


class SetOrderAttribute implements \Magento\Framework\Event\ObserverInterface
{

    protected $_request;
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_request = $request;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
       try {
           if (isset($this->_request->getParams()['order']['account']['purchase_order']) && !empty($this->_request->getParams()['order']['account']['purchase_order'])) {
               $purchaseOrder = $this->_request->getParams()['order']['account']['purchase_order'];
               $order->setPurchaseOrder($purchaseOrder);
           }
       } catch (\Exception $exception) {
           $this->logger->debug($exception);
       }

        return $this;
    }
}
