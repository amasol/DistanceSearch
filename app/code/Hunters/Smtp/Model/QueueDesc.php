<?php
namespace Hunters\Smtp\Model;

class QueueDesc extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'mail_queue_desc';

	protected $_cacheTag = 'mail_queue_desc';

	protected $_eventPrefix = 'mail_queue_desc';

	protected function _construct()
	{
		$this->_init('Hunters\Smtp\Model\ResourceModel\QueueDesc');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}
