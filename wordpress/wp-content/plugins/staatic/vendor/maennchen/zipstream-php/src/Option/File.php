<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use DateTime;
use DateTimeInterface;
final class File
{
    private $comment = '';
    private $method;
    private $deflateLevel;
    private $time;
    private $size = 0;
    /**
     * @return void
     */
    public function defaultTo(Archive $archiveOptions)
    {
        $this->deflateLevel = $this->deflateLevel ?: $archiveOptions->getDeflateLevel();
        $this->time = $this->time ?: new DateTime();
    }
    public function getComment() : string
    {
        return $this->comment;
    }
    /**
     * @return void
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }
    public function getMethod() : Method
    {
        return $this->method ?: Method::DEFLATE();
    }
    /**
     * @return void
     */
    public function setMethod(Method $method)
    {
        $this->method = $method;
    }
    public function getDeflateLevel() : int
    {
        return $this->deflateLevel ?: Archive::DEFAULT_DEFLATE_LEVEL;
    }
    /**
     * @return void
     */
    public function setDeflateLevel(int $deflateLevel)
    {
        $this->deflateLevel = $deflateLevel;
    }
    public function getTime() : DateTimeInterface
    {
        return $this->time;
    }
    /**
     * @return void
     */
    public function setTime(DateTimeInterface $time)
    {
        $this->time = $time;
    }
    public function getSize() : int
    {
        return $this->size;
    }
    /**
     * @return void
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }
}
