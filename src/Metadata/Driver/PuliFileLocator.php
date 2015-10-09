<?php

namespace Mi\Puli\Metadata\Driver;

use Metadata\Driver\AdvancedFileLocatorInterface;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\FileResource;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class PuliFileLocator implements AdvancedFileLocatorInterface
{
    private $dirs;
    private $repository;

    /**
     * @param ResourceRepository $repository
     * @param array              $dirs
     */
    public function __construct(ResourceRepository $repository, array $dirs)
    {
        $this->repository = $repository;
        $this->dirs = $dirs;
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
        foreach ($this->dirs as $prefix => $dir) {
            $nsPrefix = $prefix !== '' ? $prefix.'\\' : '';

            $collection = $this->repository->find($dir.'/**/*.'.$extension);
            if ($collection->isEmpty()) {
                continue;
            }

            /** @var FileResource $resource */
            foreach ($collection as $resource) {
                $classes[] = $nsPrefix.str_replace('.', '\\', pathinfo($resource->getName(), PATHINFO_FILENAME));
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
        foreach ($this->dirs as $prefix => $dir) {
            if ('' !== $prefix && 0 !== strpos($class->getNamespaceName(), $prefix)) {
                continue;
            }

            $len = '' === $prefix ? 0 : strlen($prefix) + 1;
            $path = $dir . '/' . str_replace('\\', '.', substr($class->name, $len)) . '.' . $extension;

            if ($this->repository->contains($path)) {
                return $this->repository->get($path)->getFilesystemPath();
            }
        }

        return null;
    }
}
