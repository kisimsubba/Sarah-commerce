<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use DateTimeImmutable;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Framework\Build;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use Staatic\Framework\Deployment;
use Staatic\WordPress\Factory\DeploymentFactory;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\Build\DestinationUrlSetting;
use Staatic\WordPress\Setting\Build\PreviewUrlSetting;

final class PublicationManager implements PublicationManagerInterface
{
    /**
     * @var BuildRepositoryInterface
     */
    private $buildRepository;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var BackgroundPublisher
     */
    private $backgroundPublisher;

    /**
     * @var DeploymentFactory
     */
    private $deploymentFactory;

    /**
     * @var DestinationUrlSetting
     */
    private $destinationUrl;

    /**
     * @var PreviewUrlSetting
     */
    private $previewUrl;

    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    public function __construct(
        BuildRepositoryInterface $buildRepository,
        PublicationRepository $publicationRepository,
        BackgroundPublisher $backgroundPublisher,
        DeploymentFactory $deploymentFactory,
        DestinationUrlSetting $destinationUrl,
        PreviewUrlSetting $previewUrl,
        SiteUrlProvider $siteUrlProvider
    )
    {
        $this->buildRepository = $buildRepository;
        $this->publicationRepository = $publicationRepository;
        $this->backgroundPublisher = $backgroundPublisher;
        $this->deploymentFactory = $deploymentFactory;
        $this->destinationUrl = $destinationUrl;
        $this->previewUrl = $previewUrl;
        $this->siteUrlProvider = $siteUrlProvider;
    }

    public function isPublicationInProgress() : bool
    {
        return (bool) \get_option('staatic_current_publication_id');
    }

    /**
     * @param mixed[] $metadata
     * @param Build|null $build
     * @param Deployment|null $deployment
     * @param bool $isPreview
     */
    public function createPublication(
        $metadata = [],
        $build = null,
        $deployment = null,
        $isPreview = \false
    ) : Publication
    {
        $build = $build ?? $this->createBuild($isPreview);
        $deployment = $deployment ?? $this->deploymentFactory->create($build->id());
        $publication = new Publication(
            $this->publicationRepository->nextId(),
            new DateTimeImmutable(),
            $build,
            $deployment,
            $isPreview,
            \get_current_user_id() ?: null,
            $metadata
        );
        $this->publicationRepository->add($publication);

        return $publication;
    }

    /**
     * @param Publication $publication
     */
    public function claimPublication($publication) : bool
    {
        if (\get_option('staatic_current_publication_id')) {
            return \false;
        }
        \update_option('staatic_current_publication_id', $publication->id());
        \update_option('staatic_latest_publication_id', $publication->id());

        return \true;
    }

    /**
     * @param Publication $publication
     * @return void
     */
    public function cancelPublication($publication)
    {
        $publication->markCanceled();
        $this->publicationRepository->update($publication);
    }

    /**
     * @param Publication $publication
     * @return void
     */
    public function initiateBackgroundPublisher($publication)
    {
        $this->backgroundPublisher->initiate($publication);
    }

    /**
     * @param Publication $publication
     * @return void
     */
    public function cancelBackgroundPublisher($publication)
    {
        $this->backgroundPublisher->cancel($publication);
    }

    /**
     * @param bool $isPreview
     * @param string|null $parentBuildId
     */
    public function createBuild($isPreview = \false, $parentBuildId = null) : Build
    {
        $destinationUrl = $isPreview ? $this->previewUrl->value() : $this->destinationUrl->value();
        $build = new Build($this->buildRepository->nextId(), $this->siteUrlProvider->provide(), new Uri(
            $destinationUrl
        ), $parentBuildId);
        $this->buildRepository->add($build);

        return $build;
    }
}
