<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Config;

use Staatic\Vendor\Symfony\Component\Config\Resource\ResourceInterface;
use Staatic\Vendor\Symfony\Component\Config\ResourceCheckerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
class ContainerParametersResourceChecker implements ResourceCheckerInterface
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * @param \Staatic\Vendor\Symfony\Component\Config\Resource\ResourceInterface $metadata
     */
    public function supports($metadata) : bool
    {
        return $metadata instanceof ContainerParametersResource;
    }
    /**
     * @param \Staatic\Vendor\Symfony\Component\Config\Resource\ResourceInterface $resource
     * @param int $timestamp
     */
    public function isFresh($resource, $timestamp) : bool
    {
        foreach ($resource->getParameters() as $key => $value) {
            if (!$this->container->hasParameter($key) || $this->container->getParameter($key) !== $value) {
                return \false;
            }
        }
        return \true;
    }
}
