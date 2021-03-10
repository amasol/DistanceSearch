<?php
namespace Hunters\AddAdminId\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\Session as CustomerSessionModel;

class SaveAdminUserIdToOrderObserver implements ObserverInterface
{
    protected $_objectManager;
    protected $_state;
    protected $_request;
    protected $authSession;
    protected $_customerSession;

    public function __construct(
        ObjectManagerInterface $objectmanager,
        State $state,
        RequestInterface $request,
        Session $authSession,
        CustomerSessionModel $customerSession
    ) {
        $this->_objectManager   =   $objectmanager;
        $this->_state           =   $state;
        $this->_request         =   $request;
        $this->authSession      =   $authSession;
        $this->_customerSession =   $customerSession;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $adminUser          =   null;
        $requestedAdminUser =   null;
        
		if ($this->_state->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML && $requestedAdminUser) {
            $adminUser  =   $requestedAdminUser;
		} else if ($this->authSession->getUser() && $this->authSession->getUser()->getId()) {
            $adminUser  =   $this->authSession->getUser()->getId();
        } else if ($this->_customerSession->getLoggedAsCustomerAdmindId()) {
            $adminUser  =   $this->_customerSession->getLoggedAsCustomerAdmindId();
        }
        
        $observer->getOrder()->setAdminId($adminUser);

        return $this;
    }
    
}
