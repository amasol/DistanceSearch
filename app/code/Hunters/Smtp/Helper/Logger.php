<?php

namespace Hunters\Smtp\Helper;

class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
  public function log($file, $message)
  {
    file_put_contents(
      $file,
      $message . "\n\n",
      FILE_APPEND
    );
  }
}
