<?php
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Plugin\ForcedCopy;
use League\Flysystem\Plugin\ListFiles;
use Silly\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

require 'vendor/autoload.php';

$source = '/Users/Maxime Fabre/Documents/My Games/Binding of Isaac Afterbirth+ Mods';
$destination = 'Program Files (x86)/Steam/steamapps/common/The Binding of Isaac Rebirth/resources';

// Setup
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$container = new Container();
$container->delegate(new ReflectionContainer());

$container->share(Filesystem::class, function () {
    $filesystem = new Filesystem(new Local('/mnt/c'));
    $filesystem->addPlugin(new ListFiles());
    $filesystem->addPlugin(new ForcedCopy());

    return $filesystem;
});

// Console
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$app = new Application('Isaac Windows Modder');
$app->useContainer($container, true);

$app->command('install', function (OutputInterface $output, Filesystem $filesystem) use ($source, $destination) {
    $output = new SymfonyStyle(new ArrayInput([]), $output);

    // Get all mods that are only graphical
    $workshopMods = $filesystem->listContents($source);
    $workshopMods = array_filter($workshopMods, function ($mod) use ($filesystem) {
        return !$filesystem->has($mod['path'].'/main.lua') && $filesystem->has($mod['path'].'/resources');
    });

    // Rename packed folder if necessary
    if ($filesystem->has($destination.'/packed')) {
        $output->writeln('<comment>A "packed" folde found, renaming</comment>');
        $filesystem->rename($destination.'/packed', $destination.'/packed-backup');
    }

    // Install mods
    $output->title('Installing '.count($workshopMods).' mods');
    $output->progressStart(count($workshopMods));
    foreach ($workshopMods as $mod) {
        $resourcesPath = $mod['path'].'/resources';
        foreach ($filesystem->listFiles($resourcesPath, true) as $file) {
            $relativePath = str_replace($resourcesPath, null, $file['path']);
            $filesystem->forceCopy($file['path'], $destination.$relativePath);
        }

        $output->progressAdvance();
    }

    $output->progressFinish();
    $output->success('Mods installed successfully!');
})->descriptions('Copy all non LUA mods to the current resources folder');

$app->setDefaultCommand('install');
$app->run();