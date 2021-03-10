<?php

namespace Hunters\MultiFeed\Plugin\Magento\Company\Model\ResourceModel;

use Hunters\MultiFeed\Helper\Queue;
use Magento\Store\Model\StoreManagerInterface;

class Company
{

    protected $_queue;
    protected $_storeManager;

    public function __construct(
         Queue $queue,
         StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_queue = $queue;
    }

    public function afterSave(\Magento\Company\Model\ResourceModel\Company $subject, $result, $model)
    {
        try {
            $companyId = $model->getId();
            $storeId = $this->_storeManager->getStore()->getId();
            $this->_queue->add($companyId, 'account', $storeId);
        } catch (\Exception $e) {
            file_put_contents(BP . '/var/log/multiFeed.log', $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
        }

        return $result;
    }

}
