<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hunters\AvaCert\Block\Link;

use \Magento\Framework\View\Element\Html\Link;

/**
 * Class Company.
 */
class Cert extends Link implements \Magento\Customer\Block\Account\SortLinkInterface
{

    protected $dataHelper;

    public function getHref()
    {
        return $this->getUrl('avacert');
    }

    public function getLabel()
    {
        return __('Add Certificate');
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function getSelectHtml($name, $id, $options = [], $value = null, $class = '', $disabled = false) {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setName(
            $name
        )->setId(
            $id
        )->setClass(
            'select admin__control-select ' . $class
        )->setValue(
            $value
        )->setOptions(
            $options
        );

        if ($disabled) {
            $select->setExtraParams('disabled', 'disabled');
        }
        return $select->getHtml();
    }


    public function getZonesHtmlSelect($name, $id, $value = null, $class = '') {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Hunters\AvaCert\Helper\Data');
        $options = $helper->getExposureZonesOptions();

        return $this->getSelectHtml($name, $id, $options, $value, $class);
    }

    public function getExemptReasonsSelect($name, $id, $value = null, $class = '', $disabled = false) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('\Hunters\AvaCert\Helper\Data');
        $options = $helper->getExemptReasonsOptions();

        return $this->getSelectHtml($name, $id, $options, $value, $class, $disabled);
    }

}
