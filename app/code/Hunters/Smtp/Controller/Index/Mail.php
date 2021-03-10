<?php
namespace Hunters\Smtp\Controller\Index;

class Mail extends \Magento\Framework\App\Action\Action
{

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
    \Magento\Framework\ObjectManagerInterface $objectmanager

		)
	{
    $this->_objectManager = $objectmanager;

    parent::__construct($context);
	}

	public function execute()
	{
    $sender = $this->_objectManager->create("Hunters\Smtp\Helper\Sender");

		$sender->sendMail();
	}

}
