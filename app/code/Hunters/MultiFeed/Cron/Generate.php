<?php

namespace Hunters\MultiFeed\Cron;

class Generate
{
  protected $worker;

  public function __construct(
    \Hunters\MultiFeed\Helper\Queue $worker
  ){
    $this->worker = $worker;
  }
  
  public function execute()
  {
    $this->worker->generateFeeds();
    
    return $this;
  }
}