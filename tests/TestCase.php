<?php

namespace Isaac;

use Isaac\Assertions\FilesystemAssertions;
use Isaac\Providers\TestingServiceProvider;
use Isaac\Services\ContainerAwareTrait;

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
     * Setup the virtual filesystem the tests will use.
     */
    protected function setupVirtualFilesystem()
    {
        $this->cache->set('destination', '/game');
        $this->cache->set('source', '/mods');

        $this->files->createDir('/game/resources/packed');
        $this->files->put('/game/resources/achievements.xml', 'foobar');

        $this->mockMod(1, 'foobar');
        $this->mockMod(2, 'barbaz');
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

    /**
     * Mock the presence of a mod in the VFS.
     *
     * @param int    $id
     * @param string $name
     *
     * @return string
     */
    protected function mockMod(int $id, string $name)
    {
        $metadata = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<metadata>
    <id>$id</id >
    <name>$name</name>
</metadata>
XML;

        $this->files->put('/mods/'.$id.'/metadata.xml', $metadata);
        $this->files->createDir('/mods/'.$id.'/resources');
    }
}