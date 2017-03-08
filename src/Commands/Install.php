<?php

namespace Isaac\Commands;

use Isaac\Services\ModsManager;
use League\Flysystem\FilesystemInterface;

class Install extends AbstractCommand
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var ModsManager
     */
    protected $mods;

    /**
     * @param FilesystemInterface $filesystem
     * @param ModsManager         $mods
     */
    public function __construct(FilesystemInterface $filesystem, ModsManager $mods)
    {
        $this->filesystem = $filesystem;
        $this->mods = $mods;

        parent::__construct();
    }

    /**
     * @return $this
     */
    protected function configure()
    {
        return $this
            ->setName('install')
            ->setDescription('Copies non-LUA mods into your resource folder');
    }

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        // Get all mods that are only graphical
        $workshopMods = $this->mods->getGraphicalMods();

        // Rename packed folder if necessary
        if (!$this->mods->isGameUnpacked()) {
            $this->output->writeln('<comment>Game is not unpacked, unpacking</comment>');
            $this->mods->unpackGame();
            $this->filesystem->rename($this->mods->getPackedPath(), $this->mods->getPackedBackupPath());
        }

        // Install mods
        $this->output->title('Installing '.count($workshopMods).' mods');
        $this->output->progressStart(count($workshopMods));
        foreach ($workshopMods as $mod) {
            $resourcesPath = $mod->path.'/resources';
            foreach ($this->filesystem->listFiles($resourcesPath, true) as $file) {
                $relativePath = str_replace($resourcesPath, null, $file['path']);
                $this->filesystem->forceCopy($file['path'], $this->mods->getGamePath().$relativePath);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->output->success('Mods installed successfully!');
    }
}
