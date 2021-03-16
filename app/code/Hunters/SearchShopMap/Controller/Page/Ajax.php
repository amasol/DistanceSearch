<?php
namespace Hunters\SearchShopMap\Controller\Page;

use Magento\Framework\Controller\ResultFactory;
use Hunters\SearchShopMap\Service\SearchZip;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SearchZip
     */
    private $searchZip;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultFactory
     * @param SearchZip $searchZip
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Hunters\SearchShopMap\Service\SearchZip $searchZip
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->resultFactory = $resultFactory;
        $this->searchZip = $searchZip;
        parent::__construct($context);
    }

    public function execute()
    {
        /**
         * @var \Magento\Framework\Controller\Result\Raw $response
         **/
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);


        $response->setHeader('Content-type', 'text/plain');
        if ($this->getRequest()->isAjax()) {
            $getZip = $this->getRequest()->getParam('zip') ?? null;

            $zip = $this->searchZip->totalTwo($getZip);
            $response->setContents(
                $this->jsonHelper->jsonEncode(
                    [
                        'loc' => $zip,
                    ]
                )
            );
        }
        return $response;
    }
}