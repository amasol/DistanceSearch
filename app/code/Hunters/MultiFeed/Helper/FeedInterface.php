<?php


namespace Hunters\MultiFeed\Helper;


interface FeedInterface
{
    public function generate($filterIds, $storeId = null);
}