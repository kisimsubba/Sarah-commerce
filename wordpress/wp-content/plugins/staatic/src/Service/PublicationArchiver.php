<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use DateTime;
use DateTimeImmutable;
use RuntimeException;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Util\PathHelper;
use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\Vendor\ZipStream\ZipStream;
use Staatic\Vendor\ZipStream\Option\Archive as ArchiveOptions;
use Staatic\Vendor\ZipStream\Option\File as FileOptions;

final class PublicationArchiver
{
    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var ResultRepository
     */
    private $resultRepository;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    public function __construct(
        PublicationRepository $publicationRepository,
        ResultRepository $resultRepository,
        ResourceRepositoryInterface $resourceRepository
    )
    {
        $this->publicationRepository = $publicationRepository;
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * @return void
     */
    public function archive(string $publicationId)
    {
        if (!($publication = $this->publicationRepository->find($publicationId))) {
            throw new RuntimeException('Unable to find publication.');
        }
        $fileName = \sprintf('publication-%s.zip', \substr($publication->id(), -6));
        $options = new ArchiveOptions();
        $options->setSendHttpHeaders(\true);
        $zipStream = new ZipStream($fileName, $options);
        foreach ($this->resultRepository->findByBuildId($publication->build()->id()) as $result) {
            $filePath = $this->determineFilePath(
                $result->url()->getPath(),
                $publication->build()->destinationUrl()->getPath()
            );
            $resource = $this->resourceRepository->find($result->sha1());
            $fileOptions = new FileOptions();
            $fileOptions->setTime($this->toDateTime($result->dateCreated()));
            $zipStream->addFileFromPsr7Stream($filePath, $resource->content(), $fileOptions);
        }
        $zipStream->finish();
    }

    private function determineFilePath(string $uriPath, string $basePath) : string
    {
        $path = $uriPath;
        if ($basePath && $basePath !== '/' && strncmp($uriPath, $basePath, strlen($basePath)) === 0) {
            $path = \mb_substr($path, \mb_strlen(\rtrim($basePath, '/')));
        }

        return PathHelper::determineFilePath($path, \false);
    }

    private function toDateTime(DateTimeImmutable $date) : DateTime
    {
        return (new DateTime())->setTimestamp($date->getTimestamp());
    }
}
