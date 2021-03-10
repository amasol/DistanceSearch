<?php
 require dirname(__DIR__, 4) . '/bootstrap.php';

 $params = $_SERVER;
 $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);

class ManualCronRunner extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface
{

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Response\Http $response)
    {
        $this->_response = $response;
        $state->setAreaCode('adminhtml');
    }

    function launch()
    {
        $cron = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Hunters\MultiFeed\Cron\Generate');

        $cron->execute();
        return $this->_response;
    }
}

 $app = $bootstrap->createApplication('ManualCronRunner');
 $bootstrap->run($app);

