<?php

namespace Isaac\Services\Environment;

use Isaac\Services\Mods\Mod;
use Isaac\TestCase;

class PathfinderTest extends TestCase
{
    /**
     * @dataProvider provideFiles
     *
     * @param int    $mod
     * @param int    $file
     * @param string $expected
     */
    public function testCanGetModFileInResourcesPath(int $mod, int $file, string $expected)
    {
        /** @var Mod $mod */
        $mod = $this->mods->getMods()->get($mod);
        $path = $this->paths->getModFileInResources($mod, $mod->listFiles()[$file]['path']);

        $this->assertEquals($this->paths->getResourcesPath().$expected, DS.$path);
    }
    /**
     * @dataProvider provideFiles
     *
     * @param int    $mod
     * @param int    $file
     * @param string $expected
     */
    public function testCanGetModFileInResourcesBackupPath(int $mod, int $file, string $expected)
    {
        /** @var Mod $mod */
        $mod = $this->mods->getMods()->get($mod);
        $path = $this->paths->getModFileInResourcesBackup($mod, $mod->listFiles()[$file]['path']);

        $this->assertEquals($this->paths->getResourcesBackupPath().$expected, DS.$path);
    }

    public function provideFiles()
    {
        return [
            [2, 2, '/gfx/foo.png'],
            [3, 0, '/main.lua']
        ];
    }
}