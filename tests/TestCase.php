<?php

namespace Isaac;

use Isaac\Assertions\FilesystemAssertions;
use Isaac\Providers\TestingServiceProvider;
use Isaac\Services\ContainerAwareTrait;
use Isaac\Services\Mods\Mod;
use Symfony\Component\Console\Tester\CommandTester;

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
        $gamePath = $this->paths->getGamePath();
        $modsPath = $this->paths->getModsPath();

        $this->cache->set('destination', $gamePath);
        $this->cache->set('source', $modsPath);

        $this->files->createDir($this->paths->getResourcesPath().'/packed');
        $this->files->put($this->paths->getResourcesPath().'/achievements.xml', 'foobar');
        $this->files->put($this->paths->getResourcesPath().'/scripts/main.lua', 'main');

        $this->mockMod(1, 'foobar');
        $this->mockMod(2, 'barbaz');
        $this->mockMod(3, 'lua', 'lua');
        $this->mockMod(4, 'lua2', 'lua2');

        $this->files->put($modsPath.'/3/resources/gfx/foo.png', '');
    }

    ////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////// MOCKS //////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * {@inheritdoc}
     */
    protected function prophesize($classOrInterface = null, string $binding = null)
    {
        $prophecy = parent::prophesize($classOrInterface);
        $binding = $binding ?: $classOrInterface;
        if ($binding) {
            $this->container->add($binding, $prophecy->reveal());
        }

        return $prophecy;
    }

    /**
     * @param string $command
     * @param array  $input
     * @param array  $options
     *
     * @return CommandTester
     */
    protected function executeCommand(string $command, array $input = [], array $options = []): CommandTester
    {
        $tester = new CommandTester($this->container->get($command));
        $tester->execute($input, $options);

        return $tester;
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
     * Get a Mod instance for a mocked mod.
     *
     * @param int $id
     *
     * @return Mod
     */
    protected function getMockedMod(int $id): Mod
    {
        $mod = new Mod($this->paths->getModsPath().'/'.$id);
        $mod->setFilesystem($this->files);

        return $mod;
    }

    /**
     * Mock the presence of a mod in the VFS.
     *
     * @param int    $id
     * @param string $name
     * @param string $lua
     *
     * @return Mod
     */
    protected function mockMod(int $id, string $name, string $lua = ''): Mod
    {
        $metadata = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<metadata>
    <id>$id</id >
    <name>$name</name>
</metadata>
XML;

        $this->files->put($this->paths->getModsPath().'/'.$id.'/metadata.xml', $metadata);
        $this->files->createDir($this->paths->getModsPath().'/'.$id.'/resources');

        if ($lua) {
            $this->files->put($this->paths->getModsPath().'/'.$id.'/main.lua', $lua);
        }

        $mod = new Mod($this->paths->getModsPath().'/'.$id);
        $mod->setFilesystem($this->files);

        return $mod;
    }
}