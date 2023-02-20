<?php

namespace Staatic\Crawler\DomParser;

use Staatic\Vendor\voku\helper\HtmlDomParser;
final class SimpleHtmlDomParser implements DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html)
    {
        $document = new HtmlDomParser();
        return $document->loadHtml($html);
    }
    public function getHtml($document) : string
    {
        return $document->html();
    }
    /**
     * @param string $name
     */
    public function getAttribute($element, $name) : string
    {
        return $this->decodeHtmlEntities($element->getAttribute($name));
    }
    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setAttribute($element, $name, $value)
    {
        $element->setAttribute($name, $this->encodeSpecialChars($value));
    }
    public function getText($element) : string
    {
        return $this->decodeHtmlEntities($element->textContent);
    }
    /**
     * @param string $value
     * @return void
     */
    public function setText($element, $value)
    {
        $element->textContent = $this->encodeSpecialChars($value);
    }
    private function decodeHtmlEntities(string $input) : string
    {
        return \html_entity_decode($input, \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML5, 'UTF-8');
    }
    private function encodeSpecialChars(string $input) : string
    {
        return \htmlspecialchars($input, \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML5, 'UTF-8');
    }
}
