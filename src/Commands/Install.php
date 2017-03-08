<?php

namespace Isaac\Commands;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class Install extends AbstractCommand
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;

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
        $source = $this->getCache()->get('source');
        $destination = $this->getCache()->get('destination');

        // Get all mods that are only graphical
        $workshopMods = $this->filesystem->listContents($source);
        $workshopMods = array_filter($workshopMods, function ($mod) {
            return !$this->filesystem->has($mod['path'].'/main.lua') && $this->filesystem->has($mod['path'].'/resources');
        });

        // Rename packed folder if necessary
        if ($this->filesystem->has($destination.'/packed')) {
            $this->output->writeln('<comment>A "packed" folde found, renaming</comment>');
            $this->filesystem->rename($destination.'/packed', $destination.'/packed-backup');
        }

        // Install mods
        $this->output->title('Installing '.count($workshopMods).' mods');
        $this->output->progressStart(count($workshopMods));
        foreach ($workshopMods as $mod) {
            $resourcesPath = $mod['path'].'/resources';
            foreach ($this->filesystem->listFiles($resourcesPath, true) as $file) {
                $relativePath = str_replace($resourcesPath, null, $file['path']);
                $this->filesystem->forceCopy($file['path'], $destination.$relativePath);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->output->success('Mods installed successfully!');
    }
}
