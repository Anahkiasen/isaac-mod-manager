<?php

namespace Isaac\Commands;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * A container-aware command that wraps output in Symfony style.
 */
abstract class AbstractCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);

        // Preliminary setup
        $this->setup();

        return $this->fire();
    }

    /**
     * Fire the command.
     *
     * @return int|null|void
     */
    abstract protected function fire();

    /**
     * Setup the CLI application with the necessary informations.
     */
    protected function setup()
    {
        $cache = $this->getCache();
        if ($cache->has('source') && $cache->has('destination')) {
            return;
        }

        // Gather paths to folders
        $this->output->title('Before we begin I need some informations from you!');
        $source = $this->output->ask('Where are your Afterbirth+ Workshop mods located?', 'C:/Users/YourName/Documents/My Games/Binding of Isaac Afterbirth+ Mods');
        $destination = $this->output->ask('Where is Afterbirth+ installed?', 'C:/Program Files (x86)/Steam/steamapps/common/The Binding of Isaac Rebirth');

        $cache->set('source', $source);
        $cache->set('destination', $destination);
        $this->output->success('Setup completed, all good!');
    }

    /**
     * @return CacheInterface
     */
    protected function getCache(): CacheInterface
    {
        return $this->container->get(CacheInterface::class);
    }
}
