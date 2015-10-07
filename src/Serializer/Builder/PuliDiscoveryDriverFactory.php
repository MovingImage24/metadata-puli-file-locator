<?php

namespace Mi\Puli\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\Builder\DriverFactoryInterface;
use JMS\Serializer\Metadata\Driver\PhpDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use Metadata\Driver\DriverChain;
use Mi\Puli\Metadata\Driver\PuliDiscoveryFileLocator;
use Puli\Discovery\Api\Discovery;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @codeCoverageIgnore
 */
class PuliDiscoveryDriverFactory implements DriverFactoryInterface
{
    private $discovery;

    /**
     * @param Discovery $discovery
     */
    public function __construct(Discovery $discovery)
    {
        $this->discovery = $discovery;
    }

    /**
     * {@inheritdoc}
     */
    public function createDriver(array $metadataDirs, Reader $annotationReader)
    {
        $defaultDriverFactory = new DefaultDriverFactory();

        $defaultDriver = $defaultDriverFactory->createDriver($metadataDirs, $annotationReader);

        $fileLocator = new PuliDiscoveryFileLocator($this->discovery);

        $puliDriverChain = new DriverChain(
            array(
                new YamlDriver($fileLocator),
                new XmlDriver($fileLocator),
                new PhpDriver($fileLocator)
            )
        );

        return new DriverChain([$puliDriverChain, $defaultDriver]);
    }
}
