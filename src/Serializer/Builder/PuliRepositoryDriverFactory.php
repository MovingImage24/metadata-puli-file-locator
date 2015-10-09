<?php

namespace Mi\Puli\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Builder\DriverFactoryInterface;
use JMS\Serializer\Metadata\Driver\PhpDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use Metadata\Driver\DriverChain;
use Mi\Puli\Metadata\Driver\PuliFileLocator;
use Puli\Repository\Api\ResourceRepository;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @codeCoverageIgnore
 */
class PuliRepositoryDriverFactory implements DriverFactoryInterface
{
    private $repository;

    /**
     * @param ResourceRepository $repository
     */
    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function createDriver(array $metadataDirs, Reader $annotationReader)
    {
        $defaultDriverFactory = new DefaultDriverFactory();

        $defaultDriver = $defaultDriverFactory->createDriver($metadataDirs, $annotationReader);

        $fileLocator = new PuliFileLocator($this->repository, $metadataDirs);

        $puliDriverChain = new DriverChain(
            array(
                new YamlDriver($fileLocator),
                new XmlDriver($fileLocator),
                new PhpDriver($fileLocator),
            )
        );

        return new DriverChain([$puliDriverChain, $defaultDriver]);
    }
}
