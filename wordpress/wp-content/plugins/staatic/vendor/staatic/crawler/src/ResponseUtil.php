<?php

namespace Staatic\Crawler;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class ResponseUtil
{
    const JAVASCRIPT_MIME_TYPES = ['application/javascript', 'application/x-javascript', 'application/ecmascript', 'application/x-ecmascript', 'text/javascript', 'text/x-javascript', 'text/ecmascript', 'text/x-ecmascript'];
    const XML_MIME_TIMES = ['application/xml', 'application/atom+xml', 'application/rdf+xml', 'application/rss+xml', 'application/xslt+xml', 'text/xml'];
    /**
     * @param int|null $readMaximumBytes
     */
    public static function convertBodyToString(StreamInterface $bodyStream, $readMaximumBytes = null) : string
    {
        if ($readMaximumBytes) {
            $body = $bodyStream->read($readMaximumBytes);
        } else {
            $body = $bodyStream->getContents();
        }
        $bodyStream->rewind();
        return $body;
    }
    public static function parseContentTypeHeader(string $header) : array
    {
        return [\preg_match('~^([^;]+)~', $header, $matches) === 1 ? $matches[1] : null, \preg_match('~charset="?(.+?)"?;?$~i', $header, $matches) === 1 ? $matches[1] : null];
    }
    public static function getMimeType(ResponseInterface $response) : string
    {
        list($mimeType) = \explode(';', $response->getHeaderLine('Content-Type'));
        return $mimeType;
    }
    /**
     * @return UriInterface|null
     */
    public static function getRedirectUrl(ResponseInterface $response)
    {
        if (!$response->hasHeader('Location')) {
            return null;
        }
        $location = $response->getHeaderLine('Location');
        return $location ? new Uri($location) : null;
    }
    public static function isErrorResponse(ResponseInterface $response) : bool
    {
        $statusCodeCategory = (int) \floor($response->getStatusCode() / 100);
        return $statusCodeCategory === 4 || $statusCodeCategory === 5;
    }
    public static function isRedirectResponse(ResponseInterface $response) : bool
    {
        $statusCodeCategory = (int) \floor($response->getStatusCode() / 100);
        return $statusCodeCategory === 3;
    }
    public static function isJavascriptResponse(ResponseInterface $response) : bool
    {
        return \in_array(self::getMimeType($response), self::JAVASCRIPT_MIME_TYPES);
    }
    public static function isXmlResponse(ResponseInterface $response) : bool
    {
        return \in_array(self::getMimeType($response), self::XML_MIME_TIMES);
    }
}
