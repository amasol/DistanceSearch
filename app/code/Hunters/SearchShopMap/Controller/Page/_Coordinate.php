<?php

namespace Hunters\SearchShopMap\Controller\Page;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\SecurityViolationException;

class Coordinate extends Action
{

    protected $_customerAccountManager;
    protected $_escaper;
    protected $_customerSession;
    protected $_redirectFactory;

    private $helperData;

    public function __construct(
        Context $context,
        Session $session,
        AccountManagementInterface $accountManagement,
        Escaper $escaper,
        RedirectFactory $redirectFactory,


//        \Hunters\SearchShopMap\Helper\Data $helperData
    )
    {
        $this->_customerSession = $session;
        $this->_customerAccountManager = $accountManagement;
        $this->_escaper = $escaper;
        $this->_redirectFactory = $redirectFactory->create();

//        $this->helperData = $helperData;


        parent::__construct($context);
    }

    public function execute()
    {

        $this->infoView->coordinateArray();


        $message = [
            'errors' => true,
            'message' => ''
        ];

        $post = $this->getRequest()->getPostValue();
        if (empty($post)) {
            return $this->_redirectFactory->setPath('/');
        }

        $lat = $post['lat'] ?? null;
        $lng = $post['lng'] ?? null;
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

//        if ($lat && $lng) {
//            #todo  Отримати даны по кординатах ы положити в масив
//            $companyData = $this->helperData->getCompanyData(
//                $this->helperData->getCompanyIdByCoordinate($lat, $lng));
//
//            $shopData = [
//                "city" => "Kiev",
//                "country" => "Ukraine",
//                "shopName" => "Keke lol"
//            ];
//
//            $message = [
//                'errors' => false,
//                'data' => json_encode($companyData),
//                'message' => ""
//            ];
//        } else {
//            $message = [
//                'errors' => true,
//                'message' => __('Please set position data.')
//            ];
//        }

        $resultJson->setData($message);
        return $resultJson;
    }

}
