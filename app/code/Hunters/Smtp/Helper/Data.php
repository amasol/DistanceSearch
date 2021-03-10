<?php

namespace Hunters\Smtp\Helper;

/**
 * @package Magento\Smtp\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_storeManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;

    }

    /**
     * Check if the manual Smtp configuration should be used instead of using the default PHP mail configuration.
     *
     * @param null $storeId
     * @return mixed
     */
    public function isConfigurationEnabled($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSfUsername($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/sf_username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSfToken($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/sf_token', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSfPassword($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/sf_password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getWsdlFilePath($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/wsdl_file_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getLogFile($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/log_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getErrorLogFile($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/error_log_file', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSendLimit($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/email_send_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSfEmailObjectId($storeId = null)
    {
        return $this->_scopeConfig->getValue('system/smtp_configuration/sf_email_object_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getBaseUrlByMessage($message) {
        $match = [];
        preg_match('/<a class="logo" href="(.*?)"/s', $message, $match);
        $baseUrl = count($match) ? trim($match[1]) : null;

        if ($baseUrl !== null && filter_var($baseUrl, FILTER_VALIDATE_URL) !== false) {
            return $baseUrl;
        }
        return null;
    }

    public function getStoreIdByBaseUrl($baseUrl)
    {
        $stores = $this->_storeManager->getStores();

        foreach ($stores as $k => $v) {
            if ($this->_storeManager->getStore($v['store_id'])->getBaseUrl() == $baseUrl) {
                return $v['store_id'];
            }
        }
        return null;
    }

}

