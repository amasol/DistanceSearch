<?php

namespace Hunters\Smtp\Cron;

class Mail
{
  public function __construct(
    \Hunters\Smtp\Helper\Sender $sender
  ){
    $this->sender = $sender;
  }
	public function execute()
	{

    $this->sender->sendMail();
		return $this;

	}
}
