<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\UrlExtractorInterface;
use Staatic\Crawler\UrlExtractor\XmlSitemapIndexUrlExtractor;
use Staatic\Crawler\UrlExtractor\XmlSitemapUrlSetUrlExtractor;
class XmlSitemapResponseHandler extends AbstractResponseHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    const SITEMAP_XML_TAG = CrawlerInterface::TAG_SITEMAP_XML;
    const XML_MIME_TIMES = ['application/xml', 'text/xml'];
    /**
     * @var UrlExtractorInterface
     */
    private $indexExtractor;
    /**
     * @var UrlExtractorInterface
     */
    private $urlSetExtractor;
    public function __construct()
    {
        $this->logger = new NullLogger();
        $this->indexExtractor = new XmlSitemapIndexUrlExtractor($this->urlFilterCallback(), $this->urlTransformCallback());
        $this->urlSetExtractor = new XmlSitemapUrlSetUrlExtractor($this->urlFilterCallback(), $this->urlTransformCallback());
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl) : CrawlUrl
    {
        $isXmlSitemapResponse = $this->isXmlSitemapResponse($crawlUrl);
        if ($isXmlSitemapResponse && $this->isXmlSitemapIndexResponse($crawlUrl)) {
            $crawlUrl = $this->handleXmlSitemapIndexResponse($crawlUrl);
        } elseif ($isXmlSitemapResponse && $this->isXmlSitemapUrlSetResponse($crawlUrl)) {
            $crawlUrl = $this->handleXmlSitemapUrlSetResponse($crawlUrl);
        }
        return parent::handle($crawlUrl);
    }
    private function isXmlSitemapResponse(CrawlUrl $crawlUrl) : bool
    {
        if (!$crawlUrl->hasTag(self::SITEMAP_XML_TAG)) {
            return \false;
        }
        if (!$crawlUrl->response()) {
            return \false;
        }
        $mimeType = ResponseUtil::getMimeType($crawlUrl->response());
        if (!\in_array($mimeType, self::XML_MIME_TIMES, \true)) {
            return \false;
        }
        return \true;
    }
    private function isXmlSitemapIndexResponse(CrawlUrl $crawlUrl) : bool
    {
        return $this->responseBodyContains($crawlUrl->response()->getBody(), '<sitemapindex');
    }
    private function isXmlSitemapUrlSetResponse(CrawlUrl $crawlUrl) : bool
    {
        return $this->responseBodyContains($crawlUrl->response()->getBody(), '<urlset');
    }
    /**
     * @param int|null $readMaximumBytes
     */
    private function responseBodyContains(StreamInterface $bodyStream, string $search, $readMaximumBytes = 4096) : bool
    {
        $responseBody = ResponseUtil::convertBodyToString($bodyStream, $readMaximumBytes);
        return strpos($responseBody, $search) !== false;
    }
    private function handleXmlSitemapIndexResponse(CrawlUrl $crawlUrl) : CrawlUrl
    {
        $readMaximumBytes = $this->crawler->crawlOptions()->maxResponseBodyInBytes();
        $responseBody = ResponseUtil::convertBodyToString($crawlUrl->response()->getBody(), $readMaximumBytes);
        $generator = $this->indexExtractor->extract($responseBody, $crawlUrl->url());
        $this->processExtractedUrls($crawlUrl, $generator, \true);
        $responseBody = Utils::streamFor($generator->getReturn());
        return $crawlUrl->withResponse($crawlUrl->response()->withBody($responseBody));
    }
    private function handleXmlSitemapUrlSetResponse(CrawlUrl $crawlUrl) : CrawlUrl
    {
        $readMaximumBytes = $this->crawler->crawlOptions()->maxResponseBodyInBytes();
        $responseBody = ResponseUtil::convertBodyToString($crawlUrl->response()->getBody(), $readMaximumBytes);
        $generator = $this->urlSetExtractor->extract($responseBody, $crawlUrl->url());
        $this->processExtractedUrls($crawlUrl, $generator);
        $responseBody = Utils::streamFor($generator->getReturn());
        return $crawlUrl->withResponse($crawlUrl->response()->withBody($responseBody));
    }
}
