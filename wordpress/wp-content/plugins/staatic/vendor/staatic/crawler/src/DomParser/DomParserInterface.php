<?php

namespace Staatic\Crawler\DomParser;

use DOMDocument;
use DOMElement;
interface DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html);
    public function getHtml($document) : string;
    /**
     * @param string $name
     */
    public function getAttribute($element, $name) : string;
    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setAttribute($element, $name, $value);
    public function getText($element) : string;
    /**
     * @param string $value
     * @return void
     */
    public function setText($element, $value);
}
