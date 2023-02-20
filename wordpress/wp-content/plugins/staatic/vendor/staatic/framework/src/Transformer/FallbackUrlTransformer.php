<?php

namespace Staatic\Framework\Transformer;

use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\FallbackUrlExtractor;
use Staatic\Crawler\UrlTransformer\UrlTransformerInterface;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
final class FallbackUrlTransformer implements TransformerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var FallbackUrlExtractor
     */
    private $extractor;
    /**
     * @param string|null $filterBasePath
     */
    public function __construct(UrlTransformerInterface $urlTransformer, $filterBasePath = null)
    {
        $this->logger = new NullLogger();
        $this->extractor = new FallbackUrlExtractor();
        $this->extractor->setTransformCallback(function (UriInterface $url, $foundOnUrl = null) use ($urlTransformer) {
            return $urlTransformer->transform($url, $foundOnUrl);
        });
        $this->extractor->setFilterBasePath($filterBasePath);
    }
    /**
     * @param Result $result
     */
    public function supports($result) : bool
    {
        if (!$result->size()) {
            return \false;
        }
        if (!$result->originalUrl()) {
            return \false;
        }
        $supportedMimeTypes = \array_merge(ResponseUtil::JAVASCRIPT_MIME_TYPES, ResponseUtil::XML_MIME_TIMES, ['text/css', 'text/html']);
        return \in_array($result->mimeType(), $supportedMimeTypes);
    }
    /**
     * @param Result $result
     * @param Resource $resource
     * @return void
     */
    public function transform($result, $resource)
    {
        $this->logger->info("Applying unmatched url transformation on '{$result->url()}'");
        $generator = $this->extractor->extract((string) $resource->content(), $result->originalUrl());
        $numReplacements = $this->applyGenerator($generator);
        $this->logger->debug("Applied {$numReplacements} unmatched url replacements");
        $resource->replace(Utils::streamFor($generator->getReturn()));
        $result->syncResource($resource);
    }
    private function applyGenerator(Generator $generator) : int
    {
        $numReplacements = 0;
        while ((bool) $generator->current()) {
            $numReplacements++;
            $generator->next();
        }
        return $numReplacements;
    }
}
