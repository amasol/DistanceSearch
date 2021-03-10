<?php

namespace Hunters\AvaCert\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class AvaCertButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $url = "*";
        $data = [
            'label' => __('Multi-State Resale Certificate'),
            'on_click' => sprintf("location.href = '%s';", $url),
            'class' => 'js-avacert',
            'sort_order' => 40,
        ];

        return $data;
    }
}
