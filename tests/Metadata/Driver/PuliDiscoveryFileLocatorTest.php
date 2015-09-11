<?php

namespace Mi\Puli\Tests\Metadata\Driver;

use Mi\Puli\Metadata\Driver\PuliDiscoveryFileLocator;
use Puli\Discovery\Api\Binding\BindingParameter;
use Puli\Discovery\Api\Binding\BindingType;
use Puli\Discovery\InMemoryDiscovery;
use Puli\Repository\FilesystemRepository;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @covers Mi\Puli\Metadata\Driver\PuliDiscoveryFileLocator
 */
class PuliDiscoveryFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PuliDiscoveryFileLocator
     */
    private $locator;

    /**
     * @test
     */
    public function findFileForClass()
    {
        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\A\A');
        self::assertEquals(
            realpath(__DIR__ . '/Fixture/A/A.xml'),
            realpath($this->locator->findFileForClass($ref, 'xml'))
        );

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\B\B');
        self::assertNull($this->locator->findFileForClass($ref, 'xml'));

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\C\SubDir\C');
        self::assertEquals(
            realpath(__DIR__ . '/Fixture/C/SubDir.C.yml'),
            realpath($this->locator->findFileForClass($ref, 'yml'))
        );

        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\PuliFileLocatorTest');
        self::assertNull($this->locator->findFileForClass($ref, 'yml'));
    }

    /**
     * @test
     */
    public function traits()
    {
        $ref = new \ReflectionClass('Mi\Puli\Tests\Metadata\Driver\Fixture\T\T');
        self::assertEquals(
            realpath(__DIR__ . '/Fixture/T/T.xml'),
            realpath($this->locator->findFileForClass($ref, 'xml'))
        );
    }

    /**
     * @test
     */
    public function findFileForGlobalNamespacedClass()
    {
        require_once __DIR__ . '/Fixture/D/D.php';
        $ref = new \ReflectionClass('D');
        self::assertEquals(
            realpath(__DIR__ . '/Fixture/D/D.yml'),
            realpath($this->locator->findFileForClass($ref, 'yml'))
        );
    }

    /**
     * @test
     */
    public function findAllFiles()
    {
        self::assertCount(2, $xmlFiles = $this->locator->findAllClasses('xml'));
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\A\A', $xmlFiles[0]);
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\T\T', $xmlFiles[1]);

        self::assertCount(3, $ymlFiles = $this->locator->findAllClasses('yml'));
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\B\B', $ymlFiles[0]);
        self::assertSame('Mi\Puli\Tests\Metadata\Driver\Fixture\C\SubDir\C', $ymlFiles[1]);
        self::assertSame('D', $ymlFiles[2]);
    }

    protected function setUp()
    {
        $repo = new FilesystemRepository(__DIR__, true);

        $discovery = new InMemoryDiscovery($repo);
        $discovery->defineType(
            new BindingType(
                'jms/serializer-metadata',
                [
                    new BindingParameter('namespace-prefix', BindingParameter::REQUIRED),
                    new BindingParameter('extension', BindingParameter::REQUIRED)
                ]
            )
        );
        $discovery->bind(
            '/Fixture/A/*.xml',
            'jms/serializer-metadata',
            ['namespace-prefix' => 'Mi\Puli\Tests\Metadata\Driver\Fixture\A', 'extension' => 'xml']
        );
        $discovery->bind(
            '/Fixture/B/*.yml',
            'jms/serializer-metadata',
            ['namespace-prefix' => 'Mi\Puli\Tests\Metadata\Driver\Fixture\B', 'extension' => 'yml']
        );
        $discovery->bind(
            '/Fixture/C/*.yml',
            'jms/serializer-metadata',
            ['namespace-prefix' => 'Mi\Puli\Tests\Metadata\Driver\Fixture\C', 'extension' => 'yml']
        );
        $discovery->bind(
            '/Fixture/D/*.yml',
            'jms/serializer-metadata',
            ['namespace-prefix' => '', 'extension' => 'yml']
        );
        $discovery->bind(
            '/Fixture/T/*.xml',
            'jms/serializer-metadata',
            ['namespace-prefix' => 'Mi\Puli\Tests\Metadata\Driver\Fixture\T', 'extension' => 'xml']
        );

        $this->locator = new PuliDiscoveryFileLocator($discovery);
    }
}
