<?php


namespace Hunters\AvaCert\Controller\Cert;
use \Magento\Framework\Controller\ResultFactory;


class Add extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $helper;
    protected $session;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Hunters\AvaCert\Helper\Api $helper,
        \Magento\Customer\Model\Session $session

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	if(!$this->getRequest()->isAjax() || !$this->session->getCustomerId()) {
    		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    	}

    	try {
	    	$data = $this->getRequest()->getPost();
	    	$data->customers = $this->session->getCustomerId();
	    	$data->submitToStack = true;
        $result = $this->helper->add($data);
        echo json_encode($result);die;
			  $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
			  $this->getResponse()->setBody(json_encode($result));
		  } catch (\Exception $e){
		    throw new \Exception($e->getMessage());
		  }
    }
}
