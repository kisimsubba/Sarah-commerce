<?php

namespace Staatic\Crawler\DomParser;

use Staatic\Vendor\DOMWrap\Document;
final class DomWrapDomParser implements DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html)
    {
        $document = new Document();
        $document->loadHTML($html, \LIBXML_NOERROR);
        return $document;
    }
    public function getHtml($document) : string
    {
        return $document->saveHTML();
    }
    /**
     * @param string $name
     */
    public function getAttribute($element, $name) : string
    {
        return $element->getAttribute($name);
    }
    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setAttribute($element, $name, $value)
    {
        $element->setAttribute($name, $value);
    }
    public function getText($element) : string
    {
        return $element->textContent;
    }
    /**
     * @param string $value
     * @return void
     */
    public function setText($element, $value)
    {
        $element->textContent = $value;
    }
}
