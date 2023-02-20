<?php

namespace Staatic\Framework\PostProcessor;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Framework\Build;
use Staatic\Framework\ConfigGenerator\ConfigGeneratorInterface;
use Staatic\Framework\Resource;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
final class ConfigGeneratorPostProcessor implements PostProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @var ConfigGeneratorInterface
     */
    private $configGenerator;
    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, ConfigGeneratorInterface $configGenerator)
    {
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->configGenerator = $configGenerator;
        $this->logger = new NullLogger();
    }
    public function createsOrRemovesResults() : bool
    {
        return \true;
    }
    /**
     * @param Build $build
     * @return void
     */
    public function apply($build)
    {
        $this->logger->info(\sprintf('Applying config generator post processor (using %s)', \get_class($this->configGenerator)), ['buildId' => $build->id()]);
        foreach ($this->resultRepository->findByBuildId($build->id()) as $result) {
            $this->configGenerator->processResult($result);
        }
        foreach ($this->configGenerator->getFiles() as $path => $content) {
            $this->processFile($build->id(), $path, $content);
        }
    }
    /**
     * @return void
     */
    private function processFile(string $buildId, string $path, StreamInterface $content)
    {
        $resource = Resource::create($content);
        $this->resourceRepository->write($resource);
        $resultUrl = new Uri($path);
        $result = $this->resultRepository->findOneByBuildIdAndUrl($buildId, $resultUrl);
        if ($result) {
            $result->syncResource($resource);
            $this->resultRepository->update($result);
            return;
        }
        $result = Result::create($this->resultRepository->nextId(), $buildId, $resultUrl, $resource);
        $this->resultRepository->add($result);
    }
}
