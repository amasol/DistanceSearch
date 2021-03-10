<?php
namespace Hunters\Smtp\Model;

class Queue extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'mail_queue';

	protected $_cacheTag = 'mail_queue';

	protected $_eventPrefix = 'mail_queue';

	protected function _construct()
	{
		$this->_init('Hunters\Smtp\Model\ResourceModel\Queue');
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
