<?php

namespace Hunters\MultiFeed\Plugin\Magento\Customer\Model;

use Hunters\MultiFeed\Helper\Queue;

class Customer
{

    protected $_queue;

    public function __construct(
        Queue $queue
    ) {
        $this->_queue = $queue;
    }

    public function afterAfterSave(\Magento\Customer\Model\Customer $subject, $result)
    {
        $customerId = $subject->getId();
        $storeId = $subject->getStoreId();

        $this->_queue->add($customerId, 'contact', $storeId);

        return $result;
    }

}
