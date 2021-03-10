<?php
namespace Hunters\Smtp\Model\ResourceModel\Queue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'queue_id';
	protected $_eventPrefix = 'mail_queue_collection';
	protected $_eventObject = 'queue_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Hunters\Smtp\Model\Queue', 'Hunters\Smtp\Model\ResourceModel\Queue');
	}

	public function filterOrder($status, $limit)
	{
    $this->getSelect()
        ->join(
					['queue_desc' => 'mail_queue_desc'],
					'main_table.mail_desc_id = queue_desc.mail_desc_id'
    		);

    $this->getSelect()
		->where("status = '$status'")
		->limit($limit);
	}

}
