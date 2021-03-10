<?php
namespace Hunters\MultiFeed\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckOrderStatus implements ObserverInterface {

  protected $orderRepository;
  protected $queue;

  public function __construct(
    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
    \Hunters\MultiFeed\Helper\Queue $queue
  ){
      $this->orderRepository = $orderRepository;
      $this->queue = $queue;
  }

  public function execute(\Magento\Framework\Event\Observer $observer) {
      $order = $observer->getEvent()->getOrder();
      $status = $order->getStatus();
      $storeId = $order->getStoreId();
      $oldStatus = $order->getOrigData('status');

      if ($status != $oldStatus && $storeId == 1) {

        switch ($status) {
          case 'complete':
            $this->queue->add($order->getEntityId(), 'complete_orders', $storeId);
            $this->queue->add($order->getEntityId(), 'subs_orders', $storeId);
            break;

          case 'closed':
            $this->queue->add($order->getEntityId(), 'closed_orders', $storeId);
            break;

          case 'canceled':
            $this->queue->add($order->getEntityId(), 'canceled_orders', $storeId);
            break;
        }
      }
   }
}
