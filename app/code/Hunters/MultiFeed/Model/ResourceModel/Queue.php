<?php


namespace Hunters\MultiFeed\Model\ResourceModel;

class Queue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('hunters_multifeed_queue', 'queue_id');
    }
}
