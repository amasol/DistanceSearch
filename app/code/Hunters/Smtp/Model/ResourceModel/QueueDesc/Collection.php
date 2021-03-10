<?php
namespace Hunters\Smtp\Model\ResourceModel\QueueDesc;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'mail_desc_id	';
	protected $_eventPrefix = 'mail_desc_collection';
	protected $_eventObject = 'queue_mail_desc_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Hunters\Smtp\Model\QueueDesc', 'Hunters\Smtp\Model\ResourceModel\QueueDesc');
	}

}
