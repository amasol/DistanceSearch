<?php


namespace Hunters\MultiFeed\Model\ResourceModel\Queue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Hunters\MultiFeed\Model\Queue::class,
            \Hunters\MultiFeed\Model\ResourceModel\Queue::class
        );
    }
}
