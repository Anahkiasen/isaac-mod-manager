<?php

namespace Isaac;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Isaac\Assertions\FilesystemAssertions;
use Isaac\Providers\TestingServiceProvider;
use Isaac\Services\ContainerAwareTrait;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Vfs\VfsAdapter;
use Psr\Cache\CacheItemPoolInterface;
use VirtualFileSystem\FileSystem;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use ContainerAwareTrait;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->container = (new Application())->getContainer();
        $this->container->addServiceProvider(new TestingServiceProvider());

        $this->setupVirtualFilesystem();
    }

    /**
     * Setup test-related implementations of the classes we need.
     */
    protected function setupMocks(): void
    {
        $this->container->share(AdapterInterface::class, function () {
            return new VfsAdapter(new FileSystem());
        });

        $this->container->share(CacheItemPoolInterface::class, function () {
            return new ArrayCachePool();
        });
    }

    /**
     * Setup the virtual filesystem the tests will use.
     */
    protected function setupVirtualFilesystem(): void
    {
        $this->cache->set('destination', '/game');
        $this->cache->set('source', '/mods');

        $this->files->createDir('/game/resources/packed');
        $this->files->createDir('/mods');
        $this->files->put('/game/resources/achievements.xml', 'foobar');
    }

    ////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// ASSERTIONS //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @param string $file
     */
    public function assertVirtualFileExists(string $file)
    {
        $this->assertTrue($this->files->has($file));
    }

    /**
     * @param string $file
     */
    public function assertVirtualFileNotExists(string $file)
    {
        $this->assertFalse($this->files->has($file));
    }
}