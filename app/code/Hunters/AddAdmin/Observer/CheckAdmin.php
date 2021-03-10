<?php

namespace Hunters\AddAdmin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckAdmin implements ObserverInterface
{
    private $logger;
    protected $_session;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $session,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->_session = $session;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            if(!$this->_session->getUser()) {
                return $this;
            }
            
            $admin = $this->_session->getUser()->getUsername();
            $incrementId = $observer->getEvent()->getCreditmemo()->getIncrementId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('sales_creditmemo');
            $sql = 'UPDATE ' . $tableName . ' SET admin_refund = ' . '\'' . $admin . '\'' . ' where increment_id = ' . $incrementId;
            $result = $connection->query($sql);
        } catch (\Exception $exception) {
            $this->logger->critical('Error message', ['exception' => $exception]);
        }
    }
}
