<?php

namespace Hunters\AddAdmin\Helper;

class GetIdRefund
{
    protected $_request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,

        array $data = []
    )
    {
        $this->_request = $request;
    }

    public function getNameAdmin()
    {
        return $this->_request->getParam("creditmemo_id");
    }
}