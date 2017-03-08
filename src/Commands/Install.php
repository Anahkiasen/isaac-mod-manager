<?php

namespace Isaac\Commands;

use Isaac\Services\ModsManager;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Helper\ProgressBar;

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
        if (!$this->mods->areResourcesBackup()) {
            $this->output->writeln('<comment>Making a backup of resources folder, this only has to be done once</comment>');
            $this->mods->backup();
        }

        // Install mods
        $this->output->title('Installing '.count($workshopMods).' mods');
        $progress = new ProgressBar($this->output);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%'.PHP_EOL.'%message%');
        $progress->setMessage('');
        $progress->start(count($workshopMods));
        foreach ($workshopMods as $mod) {
            $progress->setMessage($mod->getName());
            $this->mods->installMod($mod);
            $progress->advance();
        }

        $progress->finish();
        $this->output->success('Mods installed successfully!');
    }
}
