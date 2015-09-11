<?php

namespace Mi\Puli\Metadata\Driver;

use Metadata\Driver\FileLocatorInterface;
use Puli\Discovery\Api\ResourceDiscovery;
use Webmozart\Glob\Glob;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class PuliDiscoveryFileLocator implements FileLocatorInterface
{
    private $discovery;

    /**
     * @param ResourceDiscovery $discovery
     */
    public function __construct(ResourceDiscovery $discovery)
    {
        $this->discovery = $discovery;
    }

    /**
     * Finds all possible metadata files.
     *
     * @param string $extension
     *
     * @return array
     */
    public function findAllClasses($extension)
    {
        $classes = array();

        foreach ($this->discovery->findByType('jms/serializer-metadata') as $binding) {
            if ($binding->getParameterValue('extension') !== $extension) {
                continue;
            }

            $nsPrefix = $binding->getParameterValue('namespace-prefix');
            $nsPrefix = $nsPrefix !== '' ? $nsPrefix.'\\' : '';

            foreach ($binding->getResources()->getNames() as $name) {
                $classes[] = $nsPrefix . str_replace('.', '\\', pathinfo($name, PATHINFO_FILENAME));
            }
        }

        return $classes;
    }

    /**
     * @param \ReflectionClass $class
     * @param string           $extension
     *
     * @return string|null
     */
    public function findFileForClass(\ReflectionClass $class, $extension)
    {
        foreach ($this->discovery->findByType('jms/serializer-metadata') as $binding) {
            $prefix = $binding->getParameterValue('namespace-prefix');
            if ($binding->getParameterValue('extension') !== $extension
                || ('' !== $prefix && 0 !== strpos($class->getNamespaceName(), $prefix))
            ) {
                continue;
            }

            $len = 0;
            if ('' !== $prefix) {
                $len = strlen($prefix) + 1;
            }

            $basePath = Glob::getBasePath($binding->getQuery());
            $path     = $basePath . '/' . str_replace('\\', '.', substr($class->name, $len)) . '.' . $extension;
            if (($key = array_search($path, $binding->getResources()->getPaths(), true)) !== false) {
                return $binding->getResources()->get($key)->getFilesystemPath();
            }
        }

        return null;
    }
}
