<?php
namespace Hunters\AvaCert\Controller\Adminhtml\Cert;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class Add extends Action {

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $helper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Hunters\AvaCert\Helper\Api $helper

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute() {
        $data = $this->getRequest()->getPost();

        $result = $this->helper->add($data);

    		$this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
    		$this->getResponse()->setBody(json_encode($result));
    }

}
