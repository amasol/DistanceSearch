<?php
namespace Hunters\Smtp\Model\ResourceModel;


class QueueDesc extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}

	protected function _construct()
	{
		$this->_init('mail_queue_desc', 'mail_desc_id');
	}

}
