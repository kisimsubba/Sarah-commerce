<?php

declare(strict_types=1);

namespace Staatic\WordPress\Factory;

use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Crawler\CrawlOptions;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\Crawler\CrawlUrlProvider\EntryCrawlUrlProvider;
use Staatic\Crawler\CrawlUrlProvider\PageNotFoundCrawlUrlProvider;
use Staatic\Crawler\Crawler;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider;
use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping;
use Staatic\Crawler\UrlTransformer\UrlTransformerInterface;
use Staatic\Framework\Build;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use Staatic\Framework\PostProcessor\AdditionalRedirectsPostProcessor;
use Staatic\Framework\PostProcessor\DuplicatesRemoverPostProcessor;
use Staatic\Framework\PostProcessor\PostProcessorCollection;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\StaticGenerator;
use Staatic\Framework\Transformer\FallbackUrlTransformer;
use Staatic\Framework\Transformer\StaaticTransformer;
use Staatic\Framework\Transformer\TransformerCollection;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Setting\Build\AdditionalPathsSetting;
use Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting;
use Staatic\WordPress\Setting\Build\AdditionalUrlsSetting;

final class StaticGeneratorFactory
{
    /**
     * The number of crawl objects to retrieve from the queue per
     * task when there are no strict time limits.
     *
     * This should not be set too high since PublishCommand needs
     * to update the progress bar once in a while.
     *
     * Relevant as well: StaticGenerator::STATS_UPDATE_FREQUENCY.
     *
     * @var int
     */
    const BATCH_SIZE_NORMAL = 300;

    /**
     * The number of crawl objects to retrieve from the queue per
     * task when there are strict time limits (e.g. 60 seconds).
     *
     * @var int
     */
    const BATCH_SIZE_CONSTRAINED = 12;

    /**
     * @var Publication
     */
    private $publication;

    /**
     * @var Build
     */
    private $build;

    /**
     * @var CrawlProfileInterface
     */
    private $crawlProfile;

    /**
     * @var UrlTransformerInterface
     */
    private $urlTransformer;

    /**
     * @var KnownUrlsContainerInterface
     */
    private $knownUrlsContainer;

    /**
     * @var TransformerCollection
     */
    private $transformers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpClientFactory
     */
    private $httpClientFactory;

    /**
     * @var CrawlProfileFactory
     */
    private $crawlProfileFactory;

    /**
     * @var CrawlQueueInterface
     */
    private $crawlQueue;

    /**
     * @var KnownUrlsContainerFactory
     */
    private $knownUrlsContainerFactory;

    /**
     * @var BuildRepositoryInterface
     */
    private $buildRepository;

    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @var UrlTransformerFactory
     */
    private $urlTransformerFactory;

    /**
     * @var HtmlUrlExtractorMapping
     */
    private $htmlUrlExtractorMapping;

    public function __construct(
        LoggerInterface $logger,
        HttpClientFactory $httpClientFactory,
        CrawlProfileFactory $crawlProfileFactory,
        CrawlQueueInterface $crawlQueue,
        KnownUrlsContainerFactory $knownUrlsContainerFactory,
        BuildRepositoryInterface $buildRepository,
        ResultRepositoryInterface $resultRepository,
        ResourceRepositoryInterface $resourceRepository,
        UrlTransformerFactory $urlTransformerFactory,
        HtmlUrlExtractorMapping $htmlUrlExtractorMapping
    )
    {
        $this->logger = $logger;
        $this->httpClientFactory = $httpClientFactory;
        $this->crawlProfileFactory = $crawlProfileFactory;
        $this->crawlQueue = $crawlQueue;
        $this->knownUrlsContainerFactory = $knownUrlsContainerFactory;
        $this->buildRepository = $buildRepository;
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->urlTransformerFactory = $urlTransformerFactory;
        $this->htmlUrlExtractorMapping = $htmlUrlExtractorMapping;
    }

    public function __invoke(Publication $publication, bool $limitedResources = \true) : StaticGenerator
    {
        $this->publication = $publication;
        $this->build = $publication->build();
        $this->knownUrlsContainer = ($this->knownUrlsContainerFactory)(!$limitedResources);
        $this->crawlProfile = ($this->crawlProfileFactory)($this->build->entryUrl(), $this->build->destinationUrl());
        $this->urlTransformer = ($this->urlTransformerFactory)($this->build->entryUrl(), $this->build->destinationUrl());
        $domParser = \get_option('staatic_crawler_dom_parser');
        $processNotFound = (bool) \get_option('staatic_crawler_process_not_found');
        $httpConcurrency = (int) \get_option('staatic_http_concurrency');
        $crawler = new Crawler($this->createHttpClient(), $this->crawlProfile, $this->crawlQueue, $this->knownUrlsContainer, new CrawlOptions([
            'concurrency' => $httpConcurrency,
            'maxCrawls' => $this->batchSize($limitedResources, $httpConcurrency),
            'maxDepth' => $this->build->parentId() ? 1 : null,
            'forceAssets' => $this->build->parentId() ? \true : \false,
            'domParser' => $domParser,
            'processNotFound' => $processNotFound,
            'htmlUrlExtractorMapping' => $this->htmlUrlExtractorMapping
        ]));
        if ($crawler instanceof LoggerAwareInterface) {
            $crawler->setLogger($this->logger);
        }
        $this->transformers = $this->createTransformers();

        return new StaticGenerator(
            $crawler,
            $this->buildRepository,
            $this->resultRepository,
            $this->resourceRepository,
            $this->transformers,
            $this->createPostProcessors(),
            $this->logger
        );
    }

    private function batchSize(bool $limitedResources, int $httpConcurrency) : int
    {
        if ($limitedResources) {
            // Maybe we could not increase the PHP time limit.
            // Maybe we are limited by the web server request time-out.
            // Maybe it is not supposed to be. :-(
            $batchSize = \min(self::BATCH_SIZE_CONSTRAINED, $httpConcurrency * 2);
        } else {
            $batchSize = self::BATCH_SIZE_NORMAL;
        }

        return \apply_filters('staatic_crawl_batch_size', $batchSize);
    }

    private function createHttpClient() : ClientInterface
    {
        $defaultHeaders = [];
        if ($this->publication->isPreview()) {
            $defaultHeaders['X-Staatic-Preview'] = $this->publication->isPreview() ? 1 : 0;
        }

        return $this->httpClientFactory->createInternalClient([
            'headers' => $defaultHeaders
        ]);
    }

    private function createTransformers() : TransformerCollection
    {
        $transformers = [];
        if ($this->build->entryUrl()->getHost() !== $this->build->destinationUrl()->getHost()) {
            // Fallback URL transformer is only supported when entry URL and destination URL have a different
            // host; otherwise transformations could occur multiple times, messing up the end result.
            $transformers[] = new FallbackUrlTransformer($this->urlTransformer, $this->build->entryUrl()->getPath());
        }
        $transformers = \apply_filters('staatic_transformers', $transformers, $this->publication);
        $transformers[] = new StaaticTransformer();
        foreach ($transformers as $transformer) {
            if ($transformer instanceof LoggerAwareInterface) {
                $transformer->setLogger($this->logger);
            }
        }

        return new TransformerCollection($transformers);
    }

    private function createPostProcessors() : PostProcessorCollection
    {
        $postProcessors = [];
        $additionalRedirects = $this->getAdditionalRedirects();
        if (\count($additionalRedirects)) {
            $postProcessors[] = new AdditionalRedirectsPostProcessor(
                $this->resultRepository,
                $this->resourceRepository,
                $additionalRedirects,
                $this->crawlProfile,
                $this->transformers
            );
        }
        $postProcessors[] = new DuplicatesRemoverPostProcessor($this->resultRepository);
        $postProcessors = \apply_filters('staatic_post_processors', $postProcessors, $this->publication);
        foreach ($postProcessors as $postProcessor) {
            if ($postProcessor instanceof LoggerAwareInterface) {
                $postProcessor->setLogger($this->logger);
            }
        }

        return new PostProcessorCollection($postProcessors);
    }

    public function createCrawlUrlProviders() : CrawlUrlProviderCollection
    {
        $providers = new CrawlUrlProviderCollection();
        $providers->addProvider(new EntryCrawlUrlProvider($this->build->entryUrl()));
        if ($notFoundPath = \get_option('staatic_page_not_found_path')) {
            $providers->addProvider(
                new PageNotFoundCrawlUrlProvider($this->build->entryUrl()->withPath($notFoundPath))
            );
        }
        $additionalUrls = $this->getAdditionalUrls();
        if (\count($additionalUrls)) {
            $providers->addProvider(new AdditionalUrlCrawlUrlProvider($additionalUrls));
        }
        $additionalPaths = $this->getAdditionalPaths();
        if (\count($additionalPaths)) {
            $excludePaths = $this->getAdditionalPathExcludes();
            foreach ($additionalPaths as $additionalPath) {
                $providers->addProvider(
                    new AdditionalPathCrawlUrlProvider($this->build->entryUrl(), \wp_normalize_path(
                        \ABSPATH
                    ), $additionalPath['path'], $excludePaths, $additionalPath['dontTouch'], $additionalPath['dontFollow'], $additionalPath['dontSave'])
                );
            }
        }
        $providers = \apply_filters('staatic_crawl_url_providers', $providers, $this->publication);
        foreach ($providers as $provider) {
            if ($provider instanceof LoggerAwareInterface) {
                $provider->setLogger($this->logger);
            }
        }

        return $providers;
    }

    private function getAdditionalRedirects() : array
    {
        $additionalRedirects = AdditionalRedirectsSetting::resolvedValue(
            \get_option('staatic_additional_redirects') ?: null
        );
        $additionalRedirects = \apply_filters('staatic_additional_redirects', $additionalRedirects);

        return $additionalRedirects;
    }

    private function getAdditionalUrls() : array
    {
        $additionalUrls = AdditionalUrlsSetting::resolvedValue(
            \get_option('staatic_additional_urls') ?: null,
            $this->build->entryUrl()
        );
        $additionalUrls = \apply_filters('staatic_additional_urls', $additionalUrls);

        return $additionalUrls;
    }

    private function getAdditionalPaths() : array
    {
        $additionalPaths = AdditionalPathsSetting::resolvedValue(\get_option('staatic_additional_paths') ?: null);
        $additionalPaths = \apply_filters('staatic_additional_paths', $additionalPaths);

        return $additionalPaths;
    }

    private function getAdditionalPathExcludes() : array
    {
        $excludePaths = [\get_option('staatic_work_directory')];
        $excludePaths = \apply_filters('staatic_additional_paths_exclude_paths', $excludePaths);

        return $excludePaths;
    }
}
