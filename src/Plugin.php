<?php

declare(strict_types=1);

namespace DigitalSector\CodeStyle;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Composer\Package\Locker;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use DigitalSector\CodeStyle\Enum\Commands;
use DigitalSector\CodeStyle\Enum\ComposerTemplates;
use Symfony\Component\Filesystem\Filesystem;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private JsonManipulator $manipulator;

    private Composer $composer;

    private IOInterface $io;

    private Filesystem $filesystem;

    public function activate(Composer $composer, IOInterface $io)
    {
        $composerFile = Factory::getComposerFile();
        $this->manipulator = new JsonManipulator(file_get_contents($composerFile));
        $this->composer = $composer;
        $this->io = $io;
        $this->filesystem = new Filesystem();
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        $this->manipulator->removeSubNode('scripts', Commands::POST_INSTALL_CMD);
        $this->manipulator->removeSubNode('scripts', Commands::POST_UPDATE_CMD);
        $this->manipulator->removeSubNode('scripts', Commands::CODE_STYLE_FIX);
        $this->manipulator->removeSubNode('scripts', Commands::CODE_STYLE_CHECK);

        $this->manipulator->removeSubNode('extra', 'hooks');

        $this->writeComposerJson();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'postInstallCmd',
            ScriptEvents::POST_UPDATE_CMD => 'postUpdateCmd',
        ];
    }

    public function postInstallCmd(): void
    {
        $this->configureProject();

        $path = $this->composer->getConfig()->get('vendor-dir');
        $stan = realpath($this->composer->getConfig()->get('vendor-dir') . '/phpstan.neon');

        var_dump($path, $stan);

        die();

        $this->io->info(sprintf('$path: %s, $stan: %s', $path, $stan));
    }

    public function postUpdateCmd(): void
    {
        $this->configureProject();

        $path = $this->composer->getConfig()->get('vendor-dir');
        $stan = realpath($this->composer->getConfig()->get('vendor-dir') . '/phpstan.neon');

        var_dump($path, $stan);

        die();

        $this->io->info(sprintf('$path: %s, $stan: %s', $path, $stan));
    }

    private function configureProject(): void
    {
        $this->manipulator->addMainKey('extra', ComposerTemplates::EXTRA_MAIN);
        $this->manipulator->addMainKey('scripts', ComposerTemplates::SCRIPTS);

        $this->writeComposerJson();

        $this->updateComposerLock();
    }

    private function writeComposerJson(): void
    {
        file_put_contents(Factory::getComposerFile(), $this->manipulator->getContents());
    }

    private function updateComposerLock(): void
    {
        $composerFile = Factory::getComposerFile();
        $composerJson = file_get_contents(Factory::getComposerFile());
        $lockFile = new JsonFile(Factory::getLockFile($composerFile), null, $this->io);
        $locker = new Locker($this->io, $lockFile, $this->composer->getInstallationManager(), $composerJson);
        $lockData = $locker->getLockData();
        $lockData['content-hash'] = Locker::getContentHash($composerJson);
        $lockFile->write($lockData);
    }
}
