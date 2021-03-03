<?php
namespace Hunters\SearchShopMap\Controller\Page;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Search extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}