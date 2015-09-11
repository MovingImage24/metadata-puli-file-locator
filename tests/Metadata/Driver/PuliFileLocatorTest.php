<?php

namespace Mi\Puli\Tests\Metadata\Driver;

use Mi\Puli\Metadata\Driver\PuliFileLocator;
use Puli\Repository\FilesystemRepository;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @covers Mi\Puli\Metadata\Driver\PuliFileLocator
 */
class PuliFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilesystemRepository
     */
    private $repo;

    /**
     * @test
     */
    public function findFileForClass()
    {
        $locator = new PuliFileLocator($this->repo, [
            'Mi\Puli\Tests\Metadata\Driver\Fixture\A' => '/Fixture/A',
            'Mi\Puli\Tests\Metadata\Driver\Fixture\B' => '/Fixture/B',
            'Mi\Puli\Tests\Metadata\Driver\Fixture\C' => '/Fixture/C'
        ]);

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\A\A');
        self::assertEquals(realpath(__DIR__.'/Fixture/A/A.xml'), realpath($locator->findFileForClass($ref, 'xml')));

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\B\B');
        self::assertNull($locator->findFileForClass($ref, 'xml'));

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\C\SubDir\C');
        self::assertEquals(realpath(__DIR__.'/Fixture/C/SubDir.C.yml'), realpath($locator->findFileForClass($ref, 'yml')));

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\PuliFileLocatorTest');
        self::assertNull($locator->findFileForClass($ref, 'yml'));
    }

    /**
     * @test
     */
    public function traits()
    {
        $locator = new PuliFileLocator($this->repo, [
            'Mi\Puli\Tests\Metadata\Driver\Fixture\T' => '/Fixture/T'
        ]);

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\T\T');
        self::assertEquals(realpath(__DIR__.'/Fixture/T/T.xml'), realpath($locator->findFileForClass($ref, 'xml')));
    }

    /**
     * @test
     */
    public function findFileForGlobalNamespacedClass()
    {
        $locator = new PuliFileLocator($this->repo, [
            '' => '/Fixture/D'
        ]);

        require_once __DIR__.'/Fixture/D/D.php';
        $ref = new \ReflectionClass('D');
        self::assertEquals(realpath(__DIR__.'/Fixture/D/D.yml'), realpath($locator->findFileForClass($ref, 'yml')));
    }

    /**
     * @test
     */
    public function findAllFiles()
    {
        $locator = new PuliFileLocator($this->repo, [
            'Mi\Puli\Tests\Metadata\Driver\Fixture\A' => '/Fixture/A',
            'Mi\Puli\Tests\Metadata\Driver\Fixture\B' => '/Fixture/B',
            'Mi\Puli\Tests\Metadata\Driver\Fixture\C' => '/Fixture/C',
            '' => '/Fixture/D'
        ]);

        self::assertCount(1, $xmlFiles = $locator->findAllClasses('xml'));
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\A\A', $xmlFiles[0]);

        self::assertCount(3, $ymlFiles = $locator->findAllClasses('yml'));
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\B\B', $ymlFiles[0]);
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\C\SubDir\C', $ymlFiles[1]);
        self::assertSame('D', $ymlFiles[2]);
    }


    protected function setUp()
    {
        $this->repo = new FilesystemRepository(__DIR__, true);
    }
}
