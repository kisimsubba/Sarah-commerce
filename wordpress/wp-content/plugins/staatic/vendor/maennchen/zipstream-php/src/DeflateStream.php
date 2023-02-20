<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream;

use Staatic\Vendor\ZipStream\Option\File;
class DeflateStream extends Stream
{
    public function __construct($stream)
    {
        parent::__construct($stream);
        \trigger_error('Class ' . __CLASS__ . ' is deprecated, delation will be handled internally instead', \E_USER_DEPRECATED);
    }
    /**
     * @return void
     */
    public function removeDeflateFilter()
    {
        \trigger_error('Method ' . __METHOD__ . ' is deprecated', \E_USER_DEPRECATED);
    }
    /**
     * @param File $options
     * @return void
     */
    public function addDeflateFilter($options)
    {
        \trigger_error('Method ' . __METHOD__ . ' is deprecated', \E_USER_DEPRECATED);
    }
}
