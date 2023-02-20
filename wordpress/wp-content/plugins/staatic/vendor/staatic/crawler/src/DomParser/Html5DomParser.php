<?php

namespace Staatic\Crawler\DomParser;

use Staatic\Vendor\Masterminds\HTML5;
final class Html5DomParser implements DomParserInterface
{
    /**
     * @param string $html
     */
    public function documentFromHtml($html)
    {
        $html5 = new HTML5(['disable_html_ns' => \true]);
        return $html5->loadHTML($html);
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
